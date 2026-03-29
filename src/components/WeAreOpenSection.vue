<template>
  <section class="k-we-are-open-section">
    <header v-if="title || subtitle" class="k-we-are-open-header">
      <div class="k-we-are-open-header-row">
        <div class="k-we-are-open-header-text">
          <k-text v-if="title">
            <h3>{{ title }}</h3>
          </k-text>
          <k-text
            v-if="subtitle"
            class="k-we-are-open-subtitle"
            v-html="subtitle"
          />
        </div>

        <!-- NEW: optional actions slot -->
        <div v-if="$slots.actions" class="k-we-are-open-header-actions">
          <slot name="actions" />
        </div>
      </div>
    </header>

    <slot />
  </section>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      default: null,
    },
    subtitle: {
      type: String,
      default: null,
    },
  },
};
</script>

<style scoped>
.k-we-are-open-section {
  background-color: var(--color-white);
  padding: 16px;
  margin-bottom: var(--spacing-16);
}

.k-we-are-open-header {
  margin: 0 0 1rem 0;
}

.k-we-are-open-subtitle {
  color: var(--color-gray-700);
  margin: 0.75rem 0 0 0;
}

/* NEW: header layout with optional actions */
.k-we-are-open-header-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: var(--spacing-12);
}

.k-we-are-open-header-text {
  min-width: 0;
}

.k-we-are-open-header-actions {
  flex: 0 0 auto;
  display: flex;
  align-items: flex-start;
}
</style>
