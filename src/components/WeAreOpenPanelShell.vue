<template>
  <k-panel-inside>
    <k-view>
      <k-header>
        {{ title }}
        <template #buttons>
          <k-button-group v-if="hasDiff">
            <k-button
              icon="check"
              variant="filled"
              theme="notice"
              :disabled="isProcessing"
              @click="$emit('submit')"
            >
              {{ $t("save") }}
            </k-button>
            <k-button
              icon="undo"
              :disabled="isProcessing"
              @click="$emit('discard')"
            >
              {{ $t("discard") }}
            </k-button>
          </k-button-group>
        </template>
      </k-header>

      <div class="k-we-are-open">
        <slot />
      </div>
    </k-view>
  </k-panel-inside>
</template>

<script>
export default {
  name: "WeAreOpenPanelShell",
  props: {
    title: { type: String, required: true },
    subtitle: { type: String, default: "" },
    hasDiff: { type: Boolean, required: true },
    isProcessing: { type: Boolean, required: true },
  },
};
</script>

<style>
.k-we-are-open {

}

.k-we-are-open-subtitle {
  margin-top: 0.5rem;
  margin-bottom: 1rem;
  color: var(--color-gray-700);
}
</style>
