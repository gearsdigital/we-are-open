import openHoursMixin from "./openHoursMixin.js";

/**
 * Shared editing/save/validation state for anything that lets a user edit
 * and persist the week's opening hours — the standalone panel view and the
 * embeddable panel section both use this, so the save-time validation gate
 * (and everything else here) can't drift between the two.
 *
 * Consumers are responsible for getting the source data (openHours,
 * defaultStartTime, defaultEndTime) onto `this` — as props (synchronous,
 * like the view) or via the section's own async load() — then calling
 * initOpenHours(source) once it's available.
 */
export default {
  mixins: [openHoursMixin],

  data() {
    return {
      localOpenHours: [],
      originalOpenHours: "[]",
      internalIsSaving: false,
    };
  },

  computed: {
    openHoursSubtitle() {
      return this.isPro
        ? this.$t("we-are-open-pro.openHours.subtitle")
        : this.$t("we-are-open.openHours.subtitle");
    },

    computedHasChanges() {
      if (typeof this.hasChanges === "boolean") return this.hasChanges;
      return JSON.stringify(this.localOpenHours) !== this.originalOpenHours;
    },

    computedIsSaving() {
      if (typeof this.isSaving === "boolean") return this.isSaving;
      return this.internalIsSaving;
    },

    hasValidationErrors() {
      return this.localOpenHours.some((day) => {
        if (day.isOpen === false) return false;
        return this.hasInvalidTimes(day.slots) || this.hasAnyOverlap(day.slots);
      });
    },
  },

  methods: {
    initOpenHours(source) {
      const local = this.initializeOpenHours(
        source,
        this.defaultStartTime,
        this.defaultEndTime
      );
      this.localOpenHours = local;
      this.originalOpenHours = JSON.stringify(local);
    },

    async save() {
      if (this.$listeners.save) {
        this.$emit("save");
        return;
      }

      if (this.hasValidationErrors) {
        this.$panel.notification.error(this.$t("we-are-open.messages.validationError"));
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
