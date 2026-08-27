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
import openHoursEditorMixin from "../mixins/openHoursEditorMixin.js";
import WeAreOpenOpenHoursSection from "./WeAreOpenOpenHoursSection.vue";
import WeAreOpenPanelShell from "./WeAreOpenPanelShell.vue";
import WeAreOpenTeaser from "./WeAreOpenTeaser.vue";

export default {
  components: {
    WeAreOpenPanelShell,
    WeAreOpenOpenHoursSection,
    OpeningHoursProTeaser: WeAreOpenTeaser,
  },
  mixins: [openHoursEditorMixin],

  props: {
    openHours: { type: Array, required: true },
    defaultStartTime: { type: String, required: true },
    defaultEndTime: { type: String, required: true },

    // Optional props for PRO version
    isPro: { type: Boolean, default: false },
    hasChanges: { type: Boolean, default: null },
    isSaving: { type: Boolean, default: null },
  },

  created() {
    this.initOpenHours(this.openHours);
  },
};
</script>
