<template>
  <k-section :label="label" class="k-we-are-open-panel-section">
    <WeAreOpenOpenHoursSection
      :subtitle="openHoursSubtitle"
      :open-hours="localOpenHours"
      :default-start-time="defaultStartTime"
      :default-end-time="defaultEndTime"
      :allow-multiple-slots="isPro"
      @update:openHours="localOpenHours = $event"
    />

    <footer v-if="computedHasChanges" class="k-we-are-open-panel-section-footer">
      <k-button-group>
        <k-button icon="check" :disabled="computedIsSaving" @click="save">
          {{ $t("save") }}
        </k-button>
        <k-button icon="undo" :disabled="computedIsSaving" @click="revert">
          {{ $t("discard") }}
        </k-button>
      </k-button-group>
    </footer>
  </k-section>
</template>

<script>
import openHoursEditorMixin from "../mixins/openHoursEditorMixin.js";
import WeAreOpenOpenHoursSection from "./WeAreOpenOpenHoursSection.vue";

/**
 * Panel section wrapper — lets We Are Open be dropped into any blueprint
 * (site.yml, a page, ...) instead of only living in its own dedicated view.
 * Sections load their own data asynchronously (this.load(), inherited from
 * Kirby's section machinery) rather than receiving it as props like the
 * standalone view does, so the shared editing logic lives in
 * openHoursEditorMixin and initOpenHours() is called once loading resolves.
 */
export default {
  components: { WeAreOpenOpenHoursSection },
  mixins: [openHoursEditorMixin],

  data() {
    return {
      label: null,
      isPro: false,
      defaultStartTime: "08:00:00",
      defaultEndTime: "17:00:00",
    };
  },

  async created() {
    const response = await this.load();
    this.label = response.label;
    this.defaultStartTime = response.defaultStartTime;
    this.defaultEndTime = response.defaultEndTime;
    this.initOpenHours(response.openHours);
  },
};
</script>

<style>
.k-we-are-open-panel-section-footer {
  display: flex;
  justify-content: flex-end;
  margin-top: 0.75rem;
}
</style>
