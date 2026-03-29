import { resolveComponent, createElementBlock, openBlock, createCommentVNode, renderSlot, createBlock, withCtx, createElementVNode, toDisplayString, normalizeClass, createVNode, Fragment, renderList, createTextVNode } from "vue";
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
        const existing = existingByWeekday[weekday];
        let slots = [];
        if (existing?.slots?.length) {
          slots = existing.slots.map((slot) => ({
            start: this.formatTime(slot.start),
            end: this.formatTime(slot.end)
          }));
        } else if (existing?.start && existing?.end) {
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
        return { weekday, slots };
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
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$7 = {
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
const _hoisted_1$3 = { class: "k-we-are-open-section" };
const _hoisted_2$2 = {
  key: 0,
  class: "k-we-are-open-header"
};
function _sfc_render$7(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_k_text = resolveComponent("k-text");
  return openBlock(), createElementBlock("section", _hoisted_1$3, [
    $props.title || $props.subtitle ? (openBlock(), createElementBlock("header", _hoisted_2$2, [
      $props.title ? (openBlock(), createBlock(_component_k_text, { key: 0 }, {
        default: withCtx(() => [
          createElementVNode(
            "h3",
            null,
            toDisplayString($props.title),
            1
            /* TEXT */
          )
        ]),
        _: 1
        /* STABLE */
      })) : createCommentVNode("v-if", true),
      $props.subtitle ? (openBlock(), createBlock(_component_k_text, {
        key: 1,
        class: "k-we-are-open-subtitle",
        innerHTML: $props.subtitle
      }, null, 8, ["innerHTML"])) : createCommentVNode("v-if", true)
    ])) : createCommentVNode("v-if", true),
    renderSlot(_ctx.$slots, "default", {}, void 0, true)
  ]);
}
const WeAreOpenSection = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["render", _sfc_render$7], ["__scopeId", "data-v-61cfa51a"], ["__file", "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenSection.vue"]]);
const _sfc_main$6 = {
  props: {
    start: { type: String, required: true },
    end: { type: String, required: true },
    hasError: { type: Boolean, default: false }
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
function _sfc_render$6(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_k_time_field = resolveComponent("k-time-field");
  const _component_k_button = resolveComponent("k-button");
  return openBlock(), createElementBlock(
    "div",
    {
      class: normalizeClass(["k-we-are-open-slot", { "has-error": $props.hasError }])
    },
    [
      createVNode(_component_k_time_field, {
        modelValue: $options.localStart,
        "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $options.localStart = $event),
        name: "time",
        label: null
      }, null, 8, ["modelValue"]),
      _cache[2] || (_cache[2] = createElementVNode(
        "span",
        { class: "k-we-are-open-separator" },
        "–",
        -1
        /* CACHED */
      )),
      createVNode(_component_k_time_field, {
        modelValue: $options.localEnd,
        "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => $options.localEnd = $event),
        name: "time",
        label: null
      }, null, 8, ["modelValue"]),
      createVNode(_component_k_button, {
        icon: "remove",
        size: "xs",
        onClick: [$options.onRemove, $options.onRemove],
        title: _ctx.$t("we-are-open.openHours.removeSlot")
      }, null, 8, ["onClick", "title"])
    ],
    2
    /* CLASS */
  );
}
const WeAreOpenTimeSlot = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["render", _sfc_render$6], ["__scopeId", "data-v-af6a5bda"], ["__file", "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenTimeSlot.vue"]]);
const _sfc_main$5 = {
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
const _hoisted_1$2 = { class: "k-we-are-open-slot-list" };
const _hoisted_2$1 = {
  key: 0,
  class: "k-we-are-open-slots"
};
const _hoisted_3$1 = {
  key: 0,
  class: "k-we-are-open-error"
};
const _hoisted_4 = {
  key: 1,
  class: "k-we-are-open-error"
};
const _hoisted_5 = {
  key: 1,
  class: "k-we-are-open-closed-label"
};
function _sfc_render$5(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_OpeningHoursTimeSlot = resolveComponent("OpeningHoursTimeSlot");
  return openBlock(), createElementBlock("div", _hoisted_1$2, [
    $props.slots.length > 0 ? (openBlock(), createElementBlock("div", _hoisted_2$1, [
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($props.slots, (slot, slotIndex) => {
          return openBlock(), createBlock(_component_OpeningHoursTimeSlot, {
            key: slotIndex,
            start: slot.start,
            end: slot.end,
            "has-error": $options.hasSlotError(slotIndex),
            "onUpdate:start": ($event) => $options.updateSlot(slotIndex, "start", $event),
            "onUpdate:end": ($event) => $options.updateSlot(slotIndex, "end", $event),
            onRemove: ($event) => $options.removeSlot(slotIndex)
          }, null, 8, ["start", "end", "has-error", "onUpdate:start", "onUpdate:end", "onRemove"]);
        }),
        128
        /* KEYED_FRAGMENT */
      )),
      $options.hasAnyOverlap($props.slots) ? (openBlock(), createElementBlock(
        "span",
        _hoisted_3$1,
        toDisplayString($props.overlapErrorMessage),
        1
        /* TEXT */
      )) : $options.hasInvalidTimes($props.slots) ? (openBlock(), createElementBlock(
        "span",
        _hoisted_4,
        toDisplayString($props.invalidTimeErrorMessage),
        1
        /* TEXT */
      )) : createCommentVNode("v-if", true)
    ])) : (openBlock(), createElementBlock(
      "span",
      _hoisted_5,
      toDisplayString($props.closedMessage),
      1
      /* TEXT */
    ))
  ]);
}
const WeAreOpenTimeSlotList = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["render", _sfc_render$5], ["__scopeId", "data-v-dbc86956"], ["__file", "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenTimeSlotList.vue"]]);
const _sfc_main$4 = {
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
const _hoisted_1$1 = { class: "k-we-are-open-cell k-we-are-open-cell--header" };
const _hoisted_2 = { class: "k-we-are-open-cell k-we-are-open-cell--slots" };
const _hoisted_3 = { class: "k-we-are-open-cell k-we-are-open-cell--action" };
function _sfc_render$4(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_TimeSlotList = resolveComponent("TimeSlotList");
  const _component_k_button = resolveComponent("k-button");
  return openBlock(), createElementBlock(
    "div",
    {
      class: normalizeClass(["k-we-are-open-row k-we-are-open-row--hours", { "k-we-are-open-row--disabled": $props.day.slots.length === 0 }])
    },
    [
      createElementVNode(
        "div",
        _hoisted_1$1,
        toDisplayString(_ctx.$t(`we-are-open.weekdays.${$props.day.weekday}`)),
        1
        /* TEXT */
      ),
      createElementVNode("div", _hoisted_2, [
        createVNode(_component_TimeSlotList, {
          slots: $props.day.slots,
          "closed-message": _ctx.$t("we-are-open.openHours.closed"),
          "overlap-error-message": _ctx.$t("we-are-open.openHours.overlapError"),
          "invalid-time-error-message": _ctx.$t("we-are-open.openHours.invalidTimeError"),
          onUpdateSlot: $options.handleUpdateSlot,
          onRemoveSlot: $options.handleRemoveSlot
        }, null, 8, ["slots", "closed-message", "overlap-error-message", "invalid-time-error-message", "onUpdateSlot", "onRemoveSlot"])
      ]),
      createElementVNode("div", _hoisted_3, [
        createVNode(_component_k_button, {
          icon: "add",
          size: "xs",
          onClick: $options.onAddSlot,
          title: $options.addButtonTitle,
          disabled: !$options.canAddSlot
        }, null, 8, ["onClick", "title", "disabled"])
      ])
    ],
    2
    /* CLASS */
  );
}
const WeAreOpenDayRow = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["render", _sfc_render$4], ["__scopeId", "data-v-887598b2"], ["__file", "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenDayRow.vue"]]);
const _sfc_main$3 = {};
function _sfc_render$3(_ctx, _cache) {
  const _component_k_box = resolveComponent("k-box");
  return openBlock(), createBlock(_component_k_box, {
    class: "k-we-are-open-pro-teaser",
    html: true,
    theme: "warning",
    icon: "sparkling",
    text: _ctx.$t("we-are-open.proTeaserText")
  }, null, 8, ["text"]);
}
const WeAreOpenTeaser = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["render", _sfc_render$3], ["__scopeId", "data-v-f7070053"], ["__file", "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenTeaser.vue"]]);
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
function _sfc_render$2(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_OpeningHoursDayRow = resolveComponent("OpeningHoursDayRow");
  const _component_WeAreOpenSection = resolveComponent("WeAreOpenSection");
  return openBlock(), createBlock(_component_WeAreOpenSection, {
    title: $props.title,
    subtitle: $props.subtitle
  }, {
    default: withCtx(() => [
      createCommentVNode("    <OpeningHoursProTeaser />"),
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($options.localOpenHours, (day, index) => {
          return openBlock(), createBlock(_component_OpeningHoursDayRow, {
            key: day.weekday,
            day,
            "default-start-time": $props.defaultStartTime,
            "default-end-time": $props.defaultEndTime,
            "allow-multiple-slots": $props.allowMultipleSlots,
            onUpdate: ($event) => $options.updateDay(index, $event)
          }, null, 8, ["day", "default-start-time", "default-end-time", "allow-multiple-slots", "onUpdate"]);
        }),
        128
        /* KEYED_FRAGMENT */
      ))
    ]),
    _: 1
    /* STABLE */
  }, 8, ["title", "subtitle"]);
}
const WeAreOpenOpenHoursSection = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["render", _sfc_render$2], ["__file", "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenOpenHoursSection.vue"]]);
const _sfc_main$1 = {
  name: "WeAreOpenPanelShell",
  props: {
    title: { type: String, required: true },
    subtitle: { type: String, default: "" },
    hasDiff: { type: Boolean, required: true },
    isProcessing: { type: Boolean, required: true }
  }
};
const _hoisted_1 = { class: "k-we-are-open" };
function _sfc_render$1(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_k_form_controls = resolveComponent("k-form-controls");
  const _component_k_header = resolveComponent("k-header");
  const _component_k_text = resolveComponent("k-text");
  const _component_k_view = resolveComponent("k-view");
  const _component_k_panel_inside = resolveComponent("k-panel-inside");
  return openBlock(), createBlock(_component_k_panel_inside, null, {
    default: withCtx(() => [
      createVNode(_component_k_view, null, {
        default: withCtx(() => [
          createVNode(_component_k_header, null, {
            buttons: withCtx(() => [
              createVNode(_component_k_form_controls, {
                "has-diff": $props.hasDiff,
                "is-processing": $props.isProcessing,
                onDiscard: _cache[0] || (_cache[0] = ($event) => _ctx.$emit("discard")),
                onSubmit: _cache[1] || (_cache[1] = ($event) => _ctx.$emit("submit"))
              }, null, 8, ["has-diff", "is-processing"])
            ]),
            default: withCtx(() => [
              createTextVNode(
                toDisplayString($props.title) + " ",
                1
                /* TEXT */
              )
            ]),
            _: 1
            /* STABLE */
          }),
          createElementVNode("div", _hoisted_1, [
            $props.subtitle ? (openBlock(), createBlock(_component_k_text, {
              key: 0,
              class: "k-we-are-open-subtitle",
              innerHTML: $props.subtitle
            }, null, 8, ["innerHTML"])) : createCommentVNode("v-if", true),
            renderSlot(_ctx.$slots, "default")
          ])
        ]),
        _: 3
        /* FORWARDED */
      })
    ]),
    _: 3
    /* FORWARDED */
  });
}
const WeAreOpenPanelShell = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["render", _sfc_render$1], ["__file", "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenPanelShell.vue"]]);
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
        await this.$api.patch("we-are-open", {
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
function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_WeAreOpenOpenHoursSection = resolveComponent("WeAreOpenOpenHoursSection");
  const _component_WeAreOpenPanelShell = resolveComponent("WeAreOpenPanelShell");
  return openBlock(), createBlock(_component_WeAreOpenPanelShell, {
    title: _ctx.$t("we-are-open.title"),
    subtitle: $options.openHoursSubtitle,
    "has-diff": $options.computedHasChanges,
    "is-processing": $options.computedIsSaving,
    onSubmit: $options.save,
    onDiscard: $options.revert
  }, {
    default: withCtx(() => [
      renderSlot(_ctx.$slots, "default", {}, () => [
        createVNode(_component_WeAreOpenOpenHoursSection, {
          title: _ctx.$t("we-are-open.openHours.title"),
          subtitle: $options.openHoursSubtitle,
          "open-hours": $data.localOpenHours,
          "default-start-time": $props.defaultStartTime,
          "default-end-time": $props.defaultEndTime,
          "allow-multiple-slots": $props.isPro,
          "onUpdate:openHours": _cache[0] || (_cache[0] = ($event) => $data.localOpenHours = $event)
        }, null, 8, ["title", "subtitle", "open-hours", "default-start-time", "default-end-time", "allow-multiple-slots"])
      ]),
      renderSlot(_ctx.$slots, "exception-days"),
      renderSlot(_ctx.$slots, "holidays")
    ]),
    _: 3
    /* FORWARDED */
  }, 8, ["title", "subtitle", "has-diff", "is-processing", "onSubmit", "onDiscard"]);
}
const WeAreOpenPanelView = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render], ["__file", "/Users/gears/Developer/x_projects/taw-koeln.de/site/plugins/we-are-open/src/components/WeAreOpenPanelView.vue"]]);
panel.plugin("gearsdigital/we-are-open", {
  components: {
    "k-we-are-open-view": WeAreOpenPanelView
  }
});
