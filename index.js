(function() {
  "use strict";
  const WEEKDAYS = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
  const openHoursMixin = {
    data() {
      return {
        isSaving: false
      };
    },
    methods: {
      /**
       * Format time string to ensure HH:MM:SS format
       */
      formatTime(time) {
        if (!time) return "";
        const timeStr = String(time);
        if (timeStr.length === 5) {
          return timeStr + ":00";
        }
        return timeStr.substring(0, 8);
      },
      /**
       * Convert time string to minutes for comparison
       */
      timeToMinutes(time) {
        if (!time) return 0;
        const [hours, minutes] = time.split(":").map(Number);
        return hours * 60 + minutes;
      },
      /**
       * Check if a slot has valid times (start before end)
       */
      isValidSlot(slot) {
        if (!slot.start || !slot.end) return true;
        const startMinutes = this.timeToMinutes(slot.start);
        const endMinutes = this.timeToMinutes(slot.end);
        return startMinutes < endMinutes;
      },
      /**
       * Check if any slots in array have invalid times
       */
      hasInvalidTimes(slots) {
        return slots.some((slot) => {
          if (!slot.start || !slot.end) return false;
          return !this.isValidSlot(slot);
        });
      },
      /**
       * Check if any slots in array have overlapping times
       */
      hasAnyOverlap(slots) {
        for (let i = 0; i < slots.length; i++) {
          const currentSlot = slots[i];
          if (!currentSlot.start || !currentSlot.end) continue;
          if (!this.isValidSlot(currentSlot)) continue;
          for (let j = i + 1; j < slots.length; j++) {
            const otherSlot = slots[j];
            if (!otherSlot.start || !otherSlot.end) continue;
            if (!this.isValidSlot(otherSlot)) continue;
            const start1 = this.timeToMinutes(currentSlot.start);
            const end1 = this.timeToMinutes(currentSlot.end);
            const start2 = this.timeToMinutes(otherSlot.start);
            const end2 = this.timeToMinutes(otherSlot.end);
            if (start1 < end2 && start2 < end1) {
              return true;
            }
          }
        }
        return false;
      },
      /**
       * Initialize openHours from props, normalizing data structure
       */
      initializeOpenHours(openHoursFromProps, defaultStartTime, defaultEndTime) {
        const existingByWeekday = {};
        if (Array.isArray(openHoursFromProps)) {
          openHoursFromProps.forEach((item, index) => {
            const key = item.weekday || WEEKDAYS[index];
            if (key) {
              existingByWeekday[key] = item;
            }
          });
        }
        return WEEKDAYS.map((weekday) => {
          var _a;
          const existing = existingByWeekday[weekday];
          let slots = [];
          let isOpen = true;
          if ((_a = existing == null ? void 0 : existing.slots) == null ? void 0 : _a.length) {
            slots = existing.slots.map((slot) => ({
              start: this.formatTime(slot.start),
              end: this.formatTime(slot.end)
            }));
          } else if ((existing == null ? void 0 : existing.start) && (existing == null ? void 0 : existing.end)) {
            slots = [
              {
                start: this.formatTime(existing.start),
                end: this.formatTime(existing.end)
              }
            ];
          }
          if (!existing && weekday !== "sat" && weekday !== "sun") {
            slots = [{ start: defaultStartTime, end: defaultEndTime }];
          }
          if (existing && typeof existing.isOpen === "boolean") {
            isOpen = existing.isOpen;
          } else if (!existing || slots.length === 0) {
            isOpen = weekday !== "sat" && weekday !== "sun";
          }
          return { weekday, slots, isOpen };
        });
      },
      /**
       * Initialize closedDays from props, normalizing data structure
       */
      initializeClosedDays(closedDaysFromProps) {
        return (closedDaysFromProps || []).map((day) => {
          let slots = [];
          if (day.slots && Array.isArray(day.slots) && day.slots.length > 0) {
            slots = day.slots.map((slot) => ({
              start: this.formatTime(slot.start),
              end: this.formatTime(slot.end)
            }));
          } else if (day.start && day.end) {
            slots = [
              {
                start: this.formatTime(day.start),
                end: this.formatTime(day.end)
              }
            ];
          }
          return {
            date: day.date || "",
            slots,
            reason: day.reason || ""
          };
        });
      },
      /**
       * Common slot update handlers
       */
      handleUpdateSlot(dayIndex, { slotIndex, field, value }) {
        this.localOpenHours[dayIndex].slots[slotIndex][field] = value;
      },
      handleRemoveSlot(dayIndex, slotIndex) {
        this.localOpenHours[dayIndex].slots.splice(slotIndex, 1);
      },
      handleAddSlot(dayIndex) {
        this.localOpenHours[dayIndex].slots.push({
          start: this.defaultStartTime,
          end: this.defaultEndTime
        });
      }
    }
  };
  function normalizeComponent(scriptExports, render, staticRenderFns, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render) {
      options.render = render;
      options.staticRenderFns = staticRenderFns;
      options._compiled = true;
    }
    if (scopeId) {
      options._scopeId = "data-v-" + scopeId;
    }
    return {
      exports: scriptExports,
      options
    };
  }
  const _sfc_main$7 = {
    props: {
      start: { type: String, required: true },
      end: { type: String, required: true },
      hasError: { type: Boolean, default: false },
      disabled: { type: Boolean, default: false }
    },
    computed: {
      localStart: {
        get() {
          return this.start;
        },
        set(value) {
          this.$emit("update:start", value);
        }
      },
      localEnd: {
        get() {
          return this.end;
        },
        set(value) {
          this.$emit("update:end", value);
        }
      }
    },
    methods: {
      onRemove(event) {
        if (event && event.preventDefault) event.preventDefault();
        if (event && event.stopPropagation) event.stopPropagation();
        this.$emit("remove");
      }
    }
  };
  var _sfc_render$7 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "k-we-are-open-slot", class: { "has-error": _vm.hasError } }, [_c("k-time-field", { attrs: { "name": "time", "label": null, "disabled": _vm.disabled }, model: { value: _vm.localStart, callback: function($$v) {
      _vm.localStart = $$v;
    }, expression: "localStart" } }), _c("span", { staticClass: "k-we-are-open-separator" }, [_vm._v(" bis ")]), _c("k-time-field", { attrs: { "name": "time", "label": null, "disabled": _vm.disabled }, model: { value: _vm.localEnd, callback: function($$v) {
      _vm.localEnd = $$v;
    }, expression: "localEnd" } }), _c("k-button", { attrs: { "icon": "remove", "size": "xs", "title": _vm.$t("we-are-open.openHours.removeSlot"), "disabled": _vm.disabled }, on: { "click": _vm.onRemove }, nativeOn: { "click": function($event) {
      return _vm.onRemove.apply(null, arguments);
    } } })], 1);
  };
  var _sfc_staticRenderFns$7 = [];
  _sfc_render$7._withStripped = true;
  var __component__$7 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$7,
    _sfc_render$7,
    _sfc_staticRenderFns$7,
    false,
    null,
    "af6a5bda"
  );
  __component__$7.options.__file = "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenTimeSlot.vue";
  const WeAreOpenTimeSlot = __component__$7.exports;
  const _sfc_main$6 = {
    components: { OpeningHoursTimeSlot: WeAreOpenTimeSlot },
    props: {
      slots: {
        type: Array,
        required: true
      },
      closedMessage: {
        type: String,
        default: "Closed"
      },
      overlapErrorMessage: {
        type: String,
        default: "Overlap detected"
      },
      invalidTimeErrorMessage: {
        type: String,
        default: "Invalid time (start must be before end)"
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },
    methods: {
      updateSlot(index, field, value) {
        this.$emit("update-slot", { slotIndex: index, field, value });
      },
      removeSlot(index) {
        this.$emit("remove-slot", index);
      },
      timeToMinutes(time) {
        if (!time) return 0;
        const [h, m] = time.split(":").map(Number);
        return h * 60 + m;
      },
      isValidSlot(slot) {
        if (!slot.start || !slot.end) return true;
        const startMinutes = this.timeToMinutes(slot.start);
        const endMinutes = this.timeToMinutes(slot.end);
        return startMinutes < endMinutes;
      },
      hasInvalidTime(index) {
        const slot = this.slots[index];
        if (!slot.start || !slot.end) return false;
        return !this.isValidSlot(slot);
      },
      hasInvalidTimes(slots) {
        return slots.some((slot) => {
          if (!slot.start || !slot.end) return false;
          return !this.isValidSlot(slot);
        });
      },
      slotsOverlap(slot1, slot2) {
        if (!slot1.start || !slot1.end || !slot2.start || !slot2.end)
          return false;
        if (!this.isValidSlot(slot1) || !this.isValidSlot(slot2)) return false;
        const start1 = this.timeToMinutes(slot1.start);
        const end1 = this.timeToMinutes(slot1.end);
        const start2 = this.timeToMinutes(slot2.start);
        const end2 = this.timeToMinutes(slot2.end);
        return start1 < end2 && start2 < end1;
      },
      hasOverlap(index) {
        const currentSlot = this.slots[index];
        if (!currentSlot.start || !currentSlot.end) return false;
        for (let i = 0; i < this.slots.length; i++) {
          if (i === index) continue;
          const otherSlot = this.slots[i];
          if (!otherSlot.start || !otherSlot.end) continue;
          if (this.slotsOverlap(currentSlot, otherSlot)) {
            return true;
          }
        }
        return false;
      },
      hasSlotError(index) {
        return this.hasOverlap(index) || this.hasInvalidTime(index);
      },
      hasAnyOverlap(slots) {
        for (let i = 0; i < slots.length; i++) {
          if (this.hasOverlap(i)) return true;
        }
        return false;
      }
    }
  };
  var _sfc_render$6 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "k-we-are-open-slot-list" }, [_vm.slots.length > 0 ? _c("div", { staticClass: "k-we-are-open-slots" }, [_vm._l(_vm.slots, function(slot, slotIndex) {
      return _c("OpeningHoursTimeSlot", { key: slot._id || `${slotIndex}-${slot.start}-${slot.end}`, attrs: { "start": slot.start, "end": slot.end, "has-error": _vm.hasSlotError(slotIndex), "disabled": _vm.disabled }, on: { "update:start": function($event) {
        return _vm.updateSlot(slotIndex, "start", $event);
      }, "update:end": function($event) {
        return _vm.updateSlot(slotIndex, "end", $event);
      }, "remove": function($event) {
        return _vm.removeSlot(slotIndex);
      } } });
    }), _vm.hasAnyOverlap(_vm.slots) ? _c("span", { staticClass: "k-we-are-open-error" }, [_vm._v(" " + _vm._s(_vm.overlapErrorMessage) + " ")]) : _vm.hasInvalidTimes(_vm.slots) ? _c("span", { staticClass: "k-we-are-open-error" }, [_vm._v(" " + _vm._s(_vm.invalidTimeErrorMessage) + " ")]) : _vm._e()], 2) : _c("span", { staticClass: "k-we-are-open-closed-label" }, [_vm._v(" " + _vm._s(_vm.closedMessage) + " ")])]);
  };
  var _sfc_staticRenderFns$6 = [];
  _sfc_render$6._withStripped = true;
  var __component__$6 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$6,
    _sfc_render$6,
    _sfc_staticRenderFns$6,
    false,
    null,
    "dbc86956"
  );
  __component__$6.options.__file = "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenTimeSlotList.vue";
  const WeAreOpenTimeSlotList = __component__$6.exports;
  const _sfc_main$5 = {
    components: { TimeSlotList: WeAreOpenTimeSlotList },
    props: {
      day: { type: Object, required: true },
      defaultStartTime: { type: String, required: true },
      defaultEndTime: { type: String, required: true },
      /**
       * FREE limitation: only a single time slot per weekday.
       * PRO can enable multiple slots per day.
       */
      allowMultipleSlots: { type: Boolean, default: false }
    },
    computed: {
      isOpen() {
        return this.day.isOpen !== false;
      },
      canAddSlot() {
        if (this.allowMultipleSlots) return true;
        return this.day.slots.length === 0;
      },
      addButtonTitle() {
        if (this.canAddSlot) {
          return this.$t("we-are-open.openHours.addSlot");
        }
        return this.$t("we-are-open.proTeaserTitle");
      }
    },
    methods: {
      toggleOpen(isOpen) {
        this.$emit("update", { ...this.day, isOpen });
      },
      emitUpdate(slots) {
        this.$emit("update", { ...this.day, slots });
      },
      handleUpdateSlot({ slotIndex, field, value }) {
        this.$emit("update-slot", { slotIndex, field, value });
        const slots = [...this.day.slots];
        slots[slotIndex] = { ...slots[slotIndex], [field]: value };
        this.emitUpdate(slots);
      },
      handleRemoveSlot(slotIndex) {
        this.$emit("remove-slot", slotIndex);
        const slots = this.day.slots.filter((_, i) => i !== slotIndex);
        this.emitUpdate(slots);
      },
      onAddSlot(event) {
        if (event && event.preventDefault) event.preventDefault();
        if (event && event.stopPropagation) event.stopPropagation();
        this.addSlot();
      },
      addSlot() {
        if (!this.canAddSlot) return;
        this.$emit("add-slot");
        this.emitUpdate([
          ...this.day.slots,
          { start: this.defaultStartTime, end: this.defaultEndTime }
        ]);
      }
    }
  };
  var _sfc_render$5 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "k-we-are-open-row k-we-are-open-row--hours", class: { "k-we-are-open-row--disabled": !_vm.isOpen } }, [_c("div", { staticClass: "k-we-are-open-cell k-we-are-open-cell--header" }, [_vm._v(" " + _vm._s(_vm.$t(`we-are-open.weekdays.${_vm.day.weekday}`)) + " ")]), _c("div", { staticClass: "k-we-are-open-cell k-we-are-open-cell--toggle" }, [_c("k-choice-input", { attrs: { "type": "checkbox", "checked": _vm.isOpen }, on: { "input": _vm.toggleOpen } })], 1), _c("div", { staticClass: "k-we-are-open-cell k-we-are-open-cell--slots" }, [_c("TimeSlotList", { attrs: { "slots": _vm.isOpen ? _vm.day.slots : [], "closed-message": _vm.$t("we-are-open.openHours.closed"), "overlap-error-message": _vm.$t("we-are-open.openHours.overlapError"), "invalid-time-error-message": _vm.$t("we-are-open.openHours.invalidTimeError"), "disabled": !_vm.isOpen }, on: { "update-slot": _vm.handleUpdateSlot, "remove-slot": _vm.handleRemoveSlot } })], 1), _c("div", { staticClass: "k-we-are-open-cell k-we-are-open-cell--action" }, [_c("k-button", { attrs: { "icon": "add", "size": "xs", "title": _vm.addButtonTitle, "disabled": !_vm.canAddSlot || !_vm.isOpen }, on: { "click": _vm.onAddSlot } })], 1)]);
  };
  var _sfc_staticRenderFns$5 = [];
  _sfc_render$5._withStripped = true;
  var __component__$5 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$5,
    _sfc_render$5,
    _sfc_staticRenderFns$5,
    false,
    null,
    "887598b2"
  );
  __component__$5.options.__file = "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenDayRow.vue";
  const WeAreOpenDayRow = __component__$5.exports;
  const _sfc_main$4 = {
    props: {
      title: {
        type: String,
        default: null
      },
      subtitle: {
        type: String,
        default: null
      }
    }
  };
  var _sfc_render$4 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("section", { staticClass: "k-we-are-open-section" }, [_vm.title || _vm.subtitle ? _c("header", { staticClass: "k-we-are-open-header" }, [_c("div", { staticClass: "k-we-are-open-header-row" }, [_c("div", { staticClass: "k-we-are-open-header-text" }, [_vm.title ? _c("k-text", [_c("h3", [_vm._v(_vm._s(_vm.title))])]) : _vm._e(), _vm.subtitle ? _c("k-text", { staticClass: "k-we-are-open-subtitle", domProps: { "innerHTML": _vm._s(_vm.subtitle) } }) : _vm._e()], 1), _vm.$slots.actions ? _c("div", { staticClass: "k-we-are-open-header-actions" }, [_vm._t("actions")], 2) : _vm._e()])]) : _vm._e(), _vm._t("default")], 2);
  };
  var _sfc_staticRenderFns$4 = [];
  _sfc_render$4._withStripped = true;
  var __component__$4 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$4,
    _sfc_render$4,
    _sfc_staticRenderFns$4,
    false,
    null,
    "61cfa51a"
  );
  __component__$4.options.__file = "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenSection.vue";
  const WeAreOpenSection = __component__$4.exports;
  const _sfc_main$3 = {
    __name: "WeAreOpenTeaser",
    setup(__props) {
      return { __sfc: true };
    }
  };
  var _sfc_render$3 = function render() {
    var _vm = this, _c = _vm._self._c;
    _vm._self._setupProxy;
    return _c("k-box", { staticClass: "k-we-are-open-pro-teaser", attrs: { "html": true, "theme": "warning", "icon": "sparkling", "text": _vm.$t("we-are-open.proTeaserText") } });
  };
  var _sfc_staticRenderFns$3 = [];
  _sfc_render$3._withStripped = true;
  var __component__$3 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$3,
    _sfc_render$3,
    _sfc_staticRenderFns$3,
    false,
    null,
    "f7070053"
  );
  __component__$3.options.__file = "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenTeaser.vue";
  const WeAreOpenTeaser = __component__$3.exports;
  const _sfc_main$2 = {
    components: {
      OpeningHoursProTeaser: WeAreOpenTeaser,
      WeAreOpenSection,
      OpeningHoursDayRow: WeAreOpenDayRow
    },
    props: {
      title: {
        type: String,
        required: true
      },
      subtitle: {
        type: String,
        default: null
      },
      openHours: {
        type: Array,
        required: true
      },
      defaultStartTime: {
        type: String,
        required: true
      },
      defaultEndTime: {
        type: String,
        required: true
      },
      allowMultipleSlots: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      localOpenHours: {
        get() {
          return this.openHours;
        },
        set(value) {
          this.$emit("update:openHours", value);
        }
      }
    },
    methods: {
      updateDay(index, updatedDay) {
        const updated = [...this.localOpenHours];
        updated.splice(index, 1, updatedDay);
        this.$emit("update:openHours", updated);
      }
    }
  };
  var _sfc_render$2 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("WeAreOpenSection", { attrs: { "title": _vm.title, "subtitle": _vm.subtitle } }, _vm._l(_vm.localOpenHours, function(day, index) {
      return _c("OpeningHoursDayRow", { key: day.weekday, attrs: { "day": day, "default-start-time": _vm.defaultStartTime, "default-end-time": _vm.defaultEndTime, "allow-multiple-slots": _vm.allowMultipleSlots }, on: { "update": function($event) {
        return _vm.updateDay(index, $event);
      } } });
    }), 1);
  };
  var _sfc_staticRenderFns$2 = [];
  _sfc_render$2._withStripped = true;
  var __component__$2 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$2,
    _sfc_render$2,
    _sfc_staticRenderFns$2,
    false,
    null,
    null
  );
  __component__$2.options.__file = "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenOpenHoursSection.vue";
  const WeAreOpenOpenHoursSection = __component__$2.exports;
  const _sfc_main$1 = {
    name: "WeAreOpenPanelShell",
    props: {
      title: { type: String, required: true },
      subtitle: { type: String, default: "" },
      hasDiff: { type: Boolean, required: true },
      isProcessing: { type: Boolean, required: true }
    }
  };
  var _sfc_render$1 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-panel-inside", [_c("k-view", [_c("k-header", { scopedSlots: _vm._u([{ key: "buttons", fn: function() {
      return [_c("k-form-controls", { attrs: { "has-diff": _vm.hasDiff, "is-processing": _vm.isProcessing }, on: { "discard": function($event) {
        return _vm.$emit("discard");
      }, "submit": function($event) {
        return _vm.$emit("submit");
      } } })];
    }, proxy: true }]) }, [_vm._v(" " + _vm._s(_vm.title) + " ")]), _c("div", { staticClass: "k-we-are-open" }, [_vm._t("default")], 2)], 1)], 1);
  };
  var _sfc_staticRenderFns$1 = [];
  _sfc_render$1._withStripped = true;
  var __component__$1 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$1,
    _sfc_render$1,
    _sfc_staticRenderFns$1,
    false,
    null,
    null
  );
  __component__$1.options.__file = "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenPanelShell.vue";
  const WeAreOpenPanelShell = __component__$1.exports;
  const _sfc_main = {
    components: {
      WeAreOpenPanelShell,
      WeAreOpenOpenHoursSection,
      OpeningHoursProTeaser: WeAreOpenTeaser
    },
    mixins: [openHoursMixin],
    props: {
      openHours: { type: Array, required: true },
      defaultStartTime: { type: String, required: true },
      defaultEndTime: { type: String, required: true },
      // Optional props for PRO version
      isPro: { type: Boolean, default: false },
      hasChanges: { type: Boolean, default: null },
      isSaving: { type: Boolean, default: null }
    },
    data() {
      return {
        localOpenHours: [],
        originalOpenHours: "[]",
        internalIsSaving: false
      };
    },
    created() {
      const local = this.initializeOpenHours(
        this.openHours,
        this.defaultStartTime,
        this.defaultEndTime
      );
      this.localOpenHours = local;
      this.originalOpenHours = JSON.stringify(local);
    },
    computed: {
      openHoursSubtitle() {
        return this.isPro ? this.$t("we-are-open-pro.openHours.subtitle") : this.$t("we-are-open.openHours.subtitle");
      },
      computedHasChanges() {
        if (this.hasChanges !== null) return this.hasChanges;
        return JSON.stringify(this.localOpenHours) !== this.originalOpenHours;
      },
      computedIsSaving() {
        if (this.isSaving !== null) return this.isSaving;
        return this.internalIsSaving;
      }
    },
    methods: {
      async save() {
        if (this.$listeners.save) {
          this.$emit("save");
          return;
        }
        this.internalIsSaving = true;
        try {
          await this.$api.patch("we-are-open/save", {
            openHours: this.localOpenHours
          });
          this.originalOpenHours = JSON.stringify(this.localOpenHours);
        } finally {
          this.internalIsSaving = false;
        }
      },
      revert() {
        if (this.$listeners.revert) {
          this.$emit("revert");
          return;
        }
        this.localOpenHours = JSON.parse(this.originalOpenHours);
      }
    }
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("WeAreOpenPanelShell", { attrs: { "title": _vm.$t("we-are-open.title"), "subtitle": _vm.openHoursSubtitle, "has-diff": _vm.computedHasChanges, "is-processing": _vm.computedIsSaving }, on: { "submit": _vm.save, "discard": _vm.revert } }, [_vm._t("default", function() {
      return [_c("WeAreOpenOpenHoursSection", { attrs: { "title": _vm.$t("we-are-open.openHours.title"), "subtitle": _vm.openHoursSubtitle, "open-hours": _vm.localOpenHours, "default-start-time": _vm.defaultStartTime, "default-end-time": _vm.defaultEndTime, "allow-multiple-slots": _vm.isPro }, on: { "update:openHours": function($event) {
        _vm.localOpenHours = $event;
      } } })];
    }), _vm._t("exception-days"), _vm._t("holidays")], 2);
  };
  var _sfc_staticRenderFns = [];
  _sfc_render._withStripped = true;
  var __component__ = /* @__PURE__ */ normalizeComponent(
    _sfc_main,
    _sfc_render,
    _sfc_staticRenderFns,
    false,
    null,
    null
  );
  __component__.options.__file = "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenPanelView.vue";
  const WeAreOpenPanelView = __component__.exports;
  panel.plugin("gearsdigital/we-are-open", {
    components: {
      "k-we-are-open-view": WeAreOpenPanelView
    }
  });
})();
