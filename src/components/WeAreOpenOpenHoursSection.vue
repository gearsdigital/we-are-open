<template>
  <WeAreOpenSection :title="title" :subtitle="subtitle">
    <OpeningHoursProTeaser v-if="!allowMultipleSlots" />
    <OpeningHoursDayRow
      v-for="(day, index) in localOpenHours"
      :key="day.weekday"
      :day="day"
      :default-start-time="defaultStartTime"
      :default-end-time="defaultEndTime"
      :allow-multiple-slots="allowMultipleSlots"
      @update="updateDay(index, $event)"
    />
  </WeAreOpenSection>
</template>

<script>
import WeAreOpenDayRow from "./WeAreOpenDayRow.vue";
import WeAreOpenSection from "./WeAreOpenSection.vue";
import OpeningHoursProTeaser from "./WeAreOpenTeaser.vue";

export default {
  components: {
    OpeningHoursProTeaser,
    WeAreOpenSection,
    OpeningHoursDayRow: WeAreOpenDayRow,
  },

  props: {
    title: {
      type: String,
      required: true,
    },
    subtitle: {
      type: String,
      default: null,
    },
    openHours: {
      type: Array,
      required: true,
    },
    defaultStartTime: {
      type: String,
      required: true,
    },
    defaultEndTime: {
      type: String,
      required: true,
    },
    allowMultipleSlots: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    localOpenHours: {
      get() {
        return this.openHours;
      },
      set(value) {
        this.$emit("update:openHours", value);
      },
    },
  },

  methods: {
    updateDay(index, updatedDay) {
      const updated = [...this.localOpenHours];
      updated.splice(index, 1, updatedDay);
      this.$emit("update:openHours", updated);
    },
  },
};
</script>
