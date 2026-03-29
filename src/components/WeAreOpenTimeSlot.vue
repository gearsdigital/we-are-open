<template>
  <div class="k-we-are-open-slot" :class="{ 'has-error': hasError }">
    <k-time-field
      v-model="localStart"
      name="time"
      :label="null"
      :disabled="disabled"
    />
    <span class="k-we-are-open-separator"> bis </span>
    <k-time-field
      v-model="localEnd"
      name="time"
      :label="null"
      :disabled="disabled"
    />
    <k-button
      icon="remove"
      size="xs"
      @click="onRemove"
      @click.native="onRemove"
      :title="$t('we-are-open.openHours.removeSlot')"
      :disabled="disabled"
    />
  </div>
</template>

<script>
export default {
  props: {
    start: { type: String, required: true },
    end: { type: String, required: true },
    hasError: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
  },

  computed: {
    localStart: {
      get() {
        return this.start;
      },
      set(value) {
        this.$emit("update:start", value);
      },
    },
    localEnd: {
      get() {
        return this.end;
      },
      set(value) {
        this.$emit("update:end", value);
      },
    },
  },

  methods: {
    onRemove(event) {
      if (event && event.preventDefault) event.preventDefault();
      if (event && event.stopPropagation) event.stopPropagation();
      this.$emit("remove");
    },
  },
};
</script>

<style scoped>
.k-we-are-open-slot {
  display: flex;
  align-items: center;
  gap: 0.125rem;
}

.k-we-are-open-separator {
  color: var(--color-gray-400);
  font-size: var(--text-xs);
  display: inline-block;
  margin: 0 1rem;
}
</style>
