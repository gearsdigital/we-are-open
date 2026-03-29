<template>
  <div
    class="k-we-are-open-row k-we-are-open-row--hours"
    :class="{ 'k-we-are-open-row--disabled': !isOpen }"
  >
    <div class="k-we-are-open-cell k-we-are-open-cell--header">
      {{ $t(`we-are-open.weekdays.${day.weekday}`) }}
    </div>

    <div class="k-we-are-open-cell k-we-are-open-cell--toggle">
      <k-choice-input type="checkbox" :checked="isOpen" @input="toggleOpen" />
    </div>

    <div class="k-we-are-open-cell k-we-are-open-cell--slots">
      <TimeSlotList
        :slots="isOpen ? day.slots : []"
        :closed-message="$t('we-are-open.openHours.closed')"
        :overlap-error-message="$t('we-are-open.openHours.overlapError')"
        :invalid-time-error-message="
          $t('we-are-open.openHours.invalidTimeError')
        "
        :disabled="!isOpen"
        @update-slot="handleUpdateSlot"
        @remove-slot="handleRemoveSlot"
      />
    </div>

    <div class="k-we-are-open-cell k-we-are-open-cell--action">
      <k-button
        icon="add"
        size="xs"
        @click="onAddSlot"
        :title="addButtonTitle"
        :disabled="!canAddSlot || !isOpen"
      />
    </div>
  </div>
</template>

<script>
import WeAreOpenTimeSlotList from "./WeAreOpenTimeSlotList.vue";

export default {
  components: { TimeSlotList: WeAreOpenTimeSlotList },

  props: {
    day: { type: Object, required: true },
    defaultStartTime: { type: String, required: true },
    defaultEndTime: { type: String, required: true },

    /**
     * FREE limitation: only a single time slot per weekday.
     * PRO can enable multiple slots per day.
     */
    allowMultipleSlots: { type: Boolean, default: false },
  },

  computed: {
    isOpen() {
      // Default to true if not explicitly set
      return this.day.isOpen !== false;
    },

    canAddSlot() {
      // PRO: unlimited
      if (this.allowMultipleSlots) return true;

      // FREE: only allow adding the first slot
      return this.day.slots.length === 0;
    },

    addButtonTitle() {
      if (this.canAddSlot) {
        return this.$t("we-are-open.openHours.addSlot");
      }

      // Re-use existing teaser translation keys to avoid introducing new strings
      return this.$t("we-are-open.proTeaserTitle");
    },
  },

  methods: {
    toggleOpen(isOpen) {
      this.$emit("update", { ...this.day, isOpen });
    },

    emitUpdate(slots) {
      // FREE event
      this.$emit("update", { ...this.day, slots });
    },

    handleUpdateSlot({ slotIndex, field, value }) {
      // PRO-style granular event (keeps Pro working)
      this.$emit("update-slot", { slotIndex, field, value });

      // FREE full-day update (keeps Free working)
      const slots = [...this.day.slots];
      slots[slotIndex] = { ...slots[slotIndex], [field]: value };
      this.emitUpdate(slots);
    },

    handleRemoveSlot(slotIndex) {
      // PRO-style granular event
      this.$emit("remove-slot", slotIndex);

      // FREE full-day update
      const slots = this.day.slots.filter((_, i) => i !== slotIndex);
      this.emitUpdate(slots);
    },

    onAddSlot(event) {
      if (event && event.preventDefault) event.preventDefault();
      if (event && event.stopPropagation) event.stopPropagation();
      this.addSlot();
    },

    addSlot() {
      // FREE: block adding more than one slot per day
      if (!this.canAddSlot) return;

      // PRO-style granular event
      this.$emit("add-slot");

      // FREE full-day update
      this.emitUpdate([
        ...this.day.slots,
        { start: this.defaultStartTime, end: this.defaultEndTime },
      ]);
    },
  },
};
</script>

<style scoped>
.k-we-are-open-row {
  display: grid;
  grid-template-columns: 100px 80px 1fr 40px;
  align-items: center;
  border-bottom: 1px solid var(--color-gray-200);
  min-height: 53px;
}
.k-we-are-open-row:last-child {
  border-bottom: none;
}

.k-we-are-open-cell {
  padding: 0.5rem 0.75rem;
  box-sizing: border-box;
  min-height: 53px;
  display: flex;
  align-items: center;
}

.k-we-are-open-cell--toggle {
  justify-content: center;
  padding: 0.5rem 0.5rem;
}

.k-we-are-open-cell--header {
  font-size: var(--text-sm);
  font-weight: 500;
  color: var(--color-gray-800);
  white-space: nowrap;
}

.k-we-are-open-cell--action {
  justify-content: flex-end;
}

.k-we-are-open-cell--action .k-button[aria-disabled="true"] {
  opacity: 0.3;
}
</style>
