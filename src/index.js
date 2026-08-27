import WeAreOpenPanelSection from "./components/WeAreOpenPanelSection.vue";
import WeAreOpenPanelView from "./components/WeAreOpenPanelView.vue";

panel.plugin("gearsdigital/we-are-open", {
  components: {
    "k-we-are-open-view": WeAreOpenPanelView,
  },
  sections: {
    openinghours: WeAreOpenPanelSection,
  },
});
