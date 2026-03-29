export const WEEKDAYS = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];

/**
 * Shared mixin for both FREE and PRO WeAreOpen components
 * Contains common logic for handling opening hours
 */
export default {
  data() {
    return {
      isSaving: false,
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
      if (!slot.start || !slot.end) return true; // Empty slots are valid for display
      const startMinutes = this.timeToMinutes(slot.start);
      const endMinutes = this.timeToMinutes(slot.end);
      return startMinutes < endMinutes;
    },

    /**
     * Check if any slots in array have invalid times
     */
    hasInvalidTimes(slots) {
      return slots.some(slot => {
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
        if (!this.isValidSlot(currentSlot)) continue; // Skip invalid slots

        for (let j = i + 1; j < slots.length; j++) {
          const otherSlot = slots[j];
          if (!otherSlot.start || !otherSlot.end) continue;
          if (!this.isValidSlot(otherSlot)) continue; // Skip invalid slots

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
        let isOpen = true;

        if (existing?.slots?.length) {
          slots = existing.slots.map((slot) => ({
            start: this.formatTime(slot.start),
            end: this.formatTime(slot.end),
          }));
        } else if (existing?.start && existing?.end) {
          slots = [
            {
              start: this.formatTime(existing.start),
              end: this.formatTime(existing.end),
            },
          ];
        }

        // Default: weekdays prefilled, weekend empty (as in your current logic)
        if (!existing && weekday !== "sat" && weekday !== "sun") {
          slots = [{ start: defaultStartTime, end: defaultEndTime }];
        }

        // Check if day has explicit isOpen property
        if (existing && typeof existing.isOpen === "boolean") {
          isOpen = existing.isOpen;
        } else if (!existing || slots.length === 0) {
          // Weekend or days without slots are closed by default
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
            end: this.formatTime(slot.end),
          }));
        } else if (day.start && day.end) {
          slots = [
            {
              start: this.formatTime(day.start),
              end: this.formatTime(day.end),
            },
          ];
        }

        return {
          date: day.date || "",
          slots,
          reason: day.reason || "",
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
        end: this.defaultEndTime,
      });
    },
  },
};
