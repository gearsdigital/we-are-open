<template>
  <WeAreOpenPanelShell
    :title="$t('we-are-open.title')"
    :subtitle="openHoursSubtitle"
    :has-diff="computedHasChanges"
    :is-processing="computedIsSaving"
    @submit="save"
    @discard="revert"
  >
    <slot>
      <WeAreOpenOpenHoursSection
        :title="$t('we-are-open.openHours.title')"
        :subtitle="openHoursSubtitle"
        :open-hours="localOpenHours"
        :default-start-time="defaultStartTime"
        :default-end-time="defaultEndTime"
        :allow-multiple-slots="isPro"
        @update:openHours="localOpenHours = $event"
      >
      </WeAreOpenOpenHoursSection>
    </slot>

    <slot name="exception-days" />
    <slot name="holidays" />
  </WeAreOpenPanelShell>
</template>

<script>
import openHoursMixin from "../mixins/openHoursMixin.js";
import WeAreOpenOpenHoursSection from "./WeAreOpenOpenHoursSection.vue";
import WeAreOpenPanelShell from "./WeAreOpenPanelShell.vue";
import WeAreOpenTeaser from "./WeAreOpenTeaser.vue";

export default {
  components: {
    WeAreOpenPanelShell,
    WeAreOpenOpenHoursSection,
    OpeningHoursProTeaser: WeAreOpenTeaser,
  },
  mixins: [openHoursMixin],

  props: {
    openHours: { type: Array, required: true },
    defaultStartTime: { type: String, required: true },
    defaultEndTime: { type: String, required: true },

    // Optional props for PRO version
    isPro: { type: Boolean, default: false },
    hasChanges: { type: Boolean, default: null },
    isSaving: { type: Boolean, default: null },
  },

  data() {
    // In Vue 2: do not call mixin methods from data(). Initialize in created().
    return {
      localOpenHours: [],
      originalOpenHours: "[]",
      internalIsSaving: false,
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
      return this.isPro
        ? this.$t("we-are-open-pro.openHours.subtitle")
        : this.$t("we-are-open.openHours.subtitle");
    },

    computedHasChanges() {
      if (this.hasChanges !== null) return this.hasChanges;
      return JSON.stringify(this.localOpenHours) !== this.originalOpenHours;
    },
    computedIsSaving() {
      if (this.isSaving !== null) return this.isSaving;
      return this.internalIsSaving;
    },
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
          openHours: this.localOpenHours,
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
    },
  },
};
</script>
