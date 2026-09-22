import { test } from "node:test";
import assert from "node:assert/strict";
import mixin from "../../src/mixins/openHoursMixin.js";

const methods = mixin.methods;

test("single-day exceptions retain their shape after saving and reloading", () => {
  for (const legacy of [{}, { dateEnd: "" }, { dateEnd: null }]) {
    const [day] = methods.initializeClosedDays.call(methods, [
      { date: "2026-09-22", reason: "Training", slots: [], ...legacy },
    ]);
    const [reloaded] = methods.initializeClosedDays.call(methods, JSON.parse(JSON.stringify([day])));
    assert.equal(Object.hasOwn(reloaded, "dateEnd"), false);
    assert.equal(reloaded.date, "2026-09-22");
    assert.equal(reloaded.reason, "Training");
  }
});

test("actual date ranges retain their end date and activation state", () => {
  const [day] = methods.initializeClosedDays.call(methods, [
    { date: "2026-09-23", dateEnd: "2026-09-25", slots: [], isActive: false },
  ]);
  assert.equal(day.dateEnd, "2026-09-25");
  assert.equal(day.isActive, false);
});
