/**
 * SISPAQ student calendar — Vuexy app/calendar layout (read-only, no Add Event)
 * Supports multiple instances via .sispaq-calendar-root
 */
'use strict';

function initSispaqCalendarRoot(root) {
  if (!root || root.dataset.calendarReady === '1') {
    return;
  }

  const calendarElId = root.dataset.calendarEl || 'calendar';
  const sidebarId = root.dataset.sidebarId || 'app-calendar-sidebar';
  const calendarEl = document.getElementById(calendarElId);

  if (!calendarEl || typeof Calendar === 'undefined' || !window.dayGridPlugin) {
    if (!root.dataset.retry) {
      root.dataset.retry = '1';
      setTimeout(function () {
        initSispaqCalendarRoot(root);
      }, 200);
    }
    return;
  }

  const direction = typeof isRtl !== 'undefined' && isRtl ? 'rtl' : 'ltr';
  const currentEvents = window.sispaqCalendarEvents || [];
  const courseColorMap = window.sispaqCourseColorMap || {};

  const appCalendarSidebar = document.getElementById(sidebarId);
  const appOverlay = root.querySelector('.app-overlay');
  const selectAll = root.querySelector('.select-all');
  const filterInputs = Array.from(root.querySelectorAll('.input-filter'));
  const inlineCalendar = root.querySelector('.inline-calendar');

  if (calendarEl._sispaqCalendar) {
    calendarEl._sispaqCalendar.destroy();
    calendarEl._sispaqCalendar = null;
  }

  function modifyToggler() {
    const fcSidebarToggleButton = calendarEl.closest('.app-calendar-content')?.querySelector('.fc-sidebarToggle-button')
      || document.querySelector('.fc-sidebarToggle-button');
    if (!fcSidebarToggleButton) {
      return;
    }
    fcSidebarToggleButton.classList.remove('fc-button-primary');
    fcSidebarToggleButton.classList.add('d-lg-none', 'd-inline-block', 'ps-0');
    while (fcSidebarToggleButton.firstChild) {
      fcSidebarToggleButton.firstChild.remove();
    }
    fcSidebarToggleButton.setAttribute('data-bs-toggle', 'sidebar');
    fcSidebarToggleButton.setAttribute('data-overlay', '');
    fcSidebarToggleButton.setAttribute('data-target', '#' + sidebarId);
    fcSidebarToggleButton.insertAdjacentHTML(
      'beforeend',
      '<i class="icon-base ti tabler-menu-2 icon-lg text-heading"></i>'
    );
  }

  function selectedCourses() {
    return filterInputs
      .filter(function (input) {
        return input.checked;
      })
      .map(function (item) {
        return String(item.getAttribute('data-value'));
      });
  }

  function fetchEvents(info, successCallback) {
    const selected = selectedCourses();
    const filtered = currentEvents.filter(function (event) {
      const courseId = event.extendedProps?.courseId;
      return courseId != null && selected.includes(String(courseId));
    });
    successCallback(filtered);

    document.querySelectorAll('.sispaq-schedule-row').forEach(function (row) {
      const courseId = row.getAttribute('data-course-id');
      row.style.display = selected.length && selected.includes(courseId) ? '' : 'none';
    });
  }

  const calendar = new Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    events: fetchEvents,
    plugins: [window.dayGridPlugin, window.interactionPlugin, window.listPlugin, window.timegridPlugin].filter(Boolean),
    editable: false,
    dragScroll: true,
    dayMaxEvents: 2,
    selectable: false,
    customButtons: {
      sidebarToggle: {
        text: 'Sidebar'
      }
    },
    headerToolbar: {
      start: 'sidebarToggle, prev,next, title',
      end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    direction: direction,
    navLinks: true,
    eventClassNames: function ({ event: calendarEvent }) {
      const courseId = calendarEvent.extendedProps?.courseId;
      const colorName = courseColorMap[courseId] || 'primary';
      return ['bg-label-' + colorName];
    },
    datesSet: function () {
      modifyToggler();
    },
    viewDidMount: function () {
      modifyToggler();
    }
  });

  calendar.render();
  calendarEl._sispaqCalendar = calendar;
  root.dataset.calendarReady = '1';
  modifyToggler();

  if (selectAll) {
    selectAll.addEventListener('click', function (e) {
      const checked = e.currentTarget.checked;
      filterInputs.forEach(function (input) {
        input.checked = checked;
      });
      calendar.refetchEvents();
    });
  }

  filterInputs.forEach(function (item) {
    item.addEventListener('click', function () {
      if (selectAll) {
        selectAll.checked = filterInputs.filter(function (i) {
          return i.checked;
        }).length === filterInputs.length;
      }
      calendar.refetchEvents();
    });
  });

  if (inlineCalendar && typeof flatpickr !== 'undefined') {
    const inlineCalInstance = inlineCalendar.flatpickr({
      monthSelectorType: 'static',
      static: true,
      inline: true
    });

    inlineCalInstance.config.onChange.push(function (date) {
      if (date[0] && typeof moment !== 'undefined') {
        calendar.changeView(calendar.view.type, moment(date[0]).format('YYYY-MM-DD'));
      } else if (date[0]) {
        calendar.gotoDate(date[0]);
      }
      modifyToggler();
      if (appCalendarSidebar) {
        appCalendarSidebar.classList.remove('show');
      }
      if (appOverlay) {
        appOverlay.classList.remove('show');
      }
    });
  }
}

function initAllSispaqCalendars() {
  document.querySelectorAll('.sispaq-calendar-root').forEach(function (root) {
    root.dataset.calendarReady = '';
    root.dataset.retry = '';
    initSispaqCalendarRoot(root);
  });
}

document.addEventListener('DOMContentLoaded', initAllSispaqCalendars);
document.addEventListener('livewire:navigated', initAllSispaqCalendars);
