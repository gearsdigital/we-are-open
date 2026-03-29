<template>
  <div class="k-we-are-open-slot-list">
    <div v-if="slots.length > 0" class="k-we-are-open-slots">
      <OpeningHoursTimeSlot
        v-for="(slot, slotIndex) in slots"
        :key="slot._id || `${slotIndex}-${slot.start}-${slot.end}`"
        :start="slot.start"
        :end="slot.end"
        :has-error="hasSlotError(slotIndex)"
        :disabled="disabled"
        @update:start="updateSlot(slotIndex, 'start', $event)"
        @update:end="updateSlot(slotIndex, 'end', $event)"
        @remove="removeSlot(slotIndex)"
      />

      <span v-if="hasAnyOverlap(slots)" class="k-we-are-open-error">
        {{ overlapErrorMessage }}
      </span>
      <span v-else-if="hasInvalidTimes(slots)" class="k-we-are-open-error">
        {{ invalidTimeErrorMessage }}
      </span>
    </div>

    <span v-else class="k-we-are-open-closed-label">
      {{ closedMessage }}
    </span>
  </div>
</template>

<script>
import WeAreOpenTimeSlot from "./WeAreOpenTimeSlot.vue";

export default {
  components: { OpeningHoursTimeSlot: WeAreOpenTimeSlot },

  props: {
    slots: {
      type: Array,
      required: true,
    },
    closedMessage: {
      type: String,
      default: "Closed",
    },
    overlapErrorMessage: {
      type: String,
      default: "Overlap detected",
    },
    invalidTimeErrorMessage: {
      type: String,
      default: "Invalid time (start must be before end)",
    },
    disabled: {
      type: Boolean,
      default: false,
    },
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
    },
  },
};
</script>

<style scoped>
.k-we-are-open-slot-list {
  min-width: 200px;
}

.k-we-are-open-slots {
  display: flex;
  width: 300px;
  align-items: center;
  gap: 0.375rem;
  flex-wrap: wrap;
  line-height: 1;
}

.k-we-are-open-error {
  font-size: var(--text-xs);
  color: var(--color-red-600);
  font-weight: 500;
}

.k-we-are-open-closed-label {
  color: var(--color-gray-500);
  display: inline-block;
  padding: 0 7px;
}
</style>
