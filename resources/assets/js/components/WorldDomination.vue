<template>
  <div class="container">
    <div class="row mb-5">
      <h1>
        Super Secret Room Manipulator
        <small class="text-muted">Only meant for Total World Domination<sup>TM</sup></small>
      </h1>
    </div>

    <form action="/exam" method="post">
      <input type="hidden" name="_token" :value="csrfToken">
      <input type="hidden" name="c" :value="formData.co2">
      <input type="hidden" name="h" :value="formData.humidity">
      <input type="hidden" name="s" :value="formData.severity">
      <input type="hidden" name="t" :value="formData.temperature">
      <input type="hidden" name="v" :value="formData.voc">
      <input type="hidden" name="tc" :value="formData.transitionTo.co2">
      <input type="hidden" name="th" :value="formData.transitionTo.humidity">
      <input type="hidden" name="ts" :value="formData.transitionTo.severity">
      <input type="hidden" name="tt" :value="formData.transitionTo.temperature">
      <input type="hidden" name="tv" :value="formData.transitionTo.voc">
      <button type="submit" class="btn btn-danger btn-lg btn-block font-weight-bold mb-5">
        E X E C U T E
      </button>
    </form>

    <div class="card">
      <div class="card-body text-center">
        <h2 class="card-title">Controls</h2>

        <h3>Room Type</h3>
        <div class="row my-3">
          <div class="col-sm" v-for="room in buttons.roomTypes" :key="room.id">
            <button
              type="button"
              class="btn btn-lg btn-block"
              :class="{
                'btn-primary': formData.severity == room.id,
                'btn-outline-primary': formData.severity != room.id
              }"
              @click="onClickRoomType(room.id)"
            >{{ room.title }}</button>
          </div>
        </div>

        <div v-if="formData.severity > 0">
          <h3>Temperature Modifier</h3>
          <div class="row my-3">
            <div class="col-sm" v-for="tempMod in buttons.tempModifiers" :key="tempMod.id">
              <button
                type="button"
                class="btn btn-lg btn-block"
                :class="{
                  'btn-warning': formData.temperature == tempMod.id,
                  'btn-outline-warning': formData.temperature != tempMod.id
                }"
                @click="onClickTemperatureModifier(tempMod.id)"
              >{{ tempMod.title }}</button>
            </div>
          </div>

          <h3>Humidity Modifier</h3>
          <div class="row my-3">
            <div class="col-sm" v-for="humMod in buttons.humModifiers" :key="humMod.id">
              <button
                type="button"
                class="btn btn-lg btn-block"
                :class="{
                  'btn-warning': formData.humidity == humMod.id,
                  'btn-outline-warning': formData.humidity != humMod.id
                }"
                @click="onClickHumidityModifier(humMod.id)"
              >{{ humMod.title }}</button>
            </div>
          </div>
        </div>

        <h3>CO<sub>2</sub> Modifier</h3>
        <div class="row my-3">
          <div class="col-sm" v-for="co2Mod in buttons.co2Modifiers" :key="co2Mod.id">
            <button
              type="button"
              class="btn btn-lg btn-block"
              :class="{
                'btn-warning': formData.co2 == co2Mod.id,
                'btn-outline-warning': formData.co2 != co2Mod.id
              }"
              @click="onClickCo2Modifier(co2Mod.id)"
            >{{ co2Mod.title }}</button>
          </div>
        </div>

        <h3>VOC Modifier</h3>
        <div class="row my-3">
          <div class="col-sm" v-for="vocMod in buttons.vocModifiers" :key="vocMod.id">
            <button
              type="button"
              class="btn btn-lg btn-block"
              :class="{
                'btn-warning': formData.voc == vocMod.id,
                'btn-outline-warning': formData.voc != vocMod.id
              }"
              @click="onClickVocModifier(vocMod.id)"
            >{{ vocMod.title }}</button>
          </div>
        </div>
      </div>
    </div>

    <div class="card my-4">
      <div class="card-body text-center">
        <h2 class="card-title">Transition</h2>

        <div class="row my-3">
          <button
            type="button"
            class="btn btn-lg btn-block"
            :class="{
              'btn-primary': formData.transitionTo.severity == -1,
              'btn-outline-primary': formData.transitionTo.severity != -1
            }"
            @click="onClickTransitionTo(-1)"
          >None</button>
        </div>

        <div class="row my-3">
          <div class="col-sm text-center" v-for="roomType in buttons.roomTypes" :key="roomType.id">
            <button
              type="button"
              class="btn btn-lg btn-block"
              :class="{
                'btn-primary': formData.transitionTo.severity == roomType.id,
                'btn-outline-primary': formData.transitionTo.severity != roomType.id
              }"
              @click="onClickTransitionTo(roomType.id)"
            >{{ roomType.title }}</button>
          </div>
        </div>

        <div v-if="formData.transitionTo.severity > 0">
          <div class="row my-3">
            <div class="col-sm text-center" v-for="tempMod in buttons.tempModifiers" :key="tempMod.id">
              <button
                type="button"
                class="btn btn-lg btn-block"
                :class="{
                  'btn-warning': formData.transitionTo.temperature == tempMod.id,
                  'btn-outline-warning': formData.transitionTo.temperature != tempMod.id
                }"
                @click="onClickTransitionModTemp(tempMod.id)"
              >{{ tempMod.title }}</button>
            </div>
          </div>

          <div class="row my-3">
            <div class="col-sm text-center" v-for="humMod in buttons.humModifiers" :key="humMod.id">
              <button
                type="button"
                class="btn btn-lg btn-block"
                :class="{
                  'btn-warning': formData.transitionTo.humidity == humMod.id,
                  'btn-outline-warning': formData.transitionTo.humidity != humMod.id
                }"
                @click="onClickTransitionModHum(humMod.id)"
              >{{ humMod.title }}</button>
            </div>
          </div>
        </div>

        <div v-if="formData.transitionTo.severity >= 0">
          <div class="row my-3">
            <div class="col-sm text-center" v-for="co2Mod in buttons.co2Modifiers" :key="co2Mod.id">
              <button
                type="button"
                class="btn btn-lg btn-block"
                :class="{
                  'btn-warning': formData.transitionTo.co2 == co2Mod.id,
                  'btn-outline-warning': formData.transitionTo.co2 != co2Mod.id
                }"
                @click="onClickTransitionModCo2(co2Mod.id)"
              >{{ co2Mod.title }}</button>
            </div>
          </div>

          <div class="row my-3">
            <div class="col-sm text-center" v-for="vocMod in buttons.vocModifiers" :key="vocMod.id">
              <button
                type="button"
                class="btn btn-lg btn-block"
                :class="{
                  'btn-warning': formData.transitionTo.voc == vocMod.id,
                  'btn-outline-warning': formData.transitionTo.voc != vocMod.id
                }"
                @click="onClickTransitionModVoc(vocMod.id)"
              >{{ vocMod.title }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'vue-world-domination',

  props: {},

  data() {
    return {
      buttons: {
        co2Modifiers: [
          {
            id: 0,
            title: 'Just Right'
          },
          {
            id: 1,
            title: 'Dizzy'
          },
          {
            id: 2,
            title: '"Sleeping"'
          }
        ],
        humModifiers: [
          {
            id: 0,
            title: 'Dry'
          },
          {
            id: 1,
            title: 'Just Right'
          },
          {
            id: 2,
            title: 'Moist'
          }
        ],
        roomTypes: [
          {
            id: 0,
            title: 'Good'
          },
          {
            id: 1,
            title: 'Bad'
          },
          {
            id: 2,
            title: 'Horrible'
          }
        ],
        tempModifiers: [
          {
            id: 0,
            title: 'Cold'
          },
          {
            id: 1,
            title: 'Just Right'
          },
          {
            id: 2,
            title: 'Warm'
          }
        ],
        vocModifiers: [
          {
            id: 0,
            title: 'Just Right'
          },
          {
            id: 1,
            title: 'Lesser Green Fog'
          },
          {
            id: 2,
            title: 'Greater Green Fog'
          }
        ]
      },
      csrfToken: '',
      formData: {
        co2: 0,
        humidity: 1,
        severity: 0,
        temperature: 1,
        transitionTo: {
          co2: 0,
          humidity: 1,
          severity: -1,
          temperature: 1,
          voc: 0
        },
        voc: 0
      }
    };
  },

  computed: {},

  watch: {},

  created: function() {
    let token = document.head.querySelector('meta[name="csrf-token"]');

    if (token) {
      this.csrfToken = token.content;
    } else {
      // TODO: Make this do something more
      console.error('CSRF token not found!');
    }
  },

  mounted: function() {},

  beforeDestroy: function() {},

  destroyed: function() {},

  methods: {
    clamp(numVal, minVal, maxVal) {
      return Math.min(maxVal, Math.max(minVal, numVal));
    },

    onClickCo2Modifier(id) {
      var cId = this.clamp(id, 0, this.buttons.co2Modifiers.length - 1);
      this.formData.co2 = cId;
    },

    onClickHumidityModifier(id) {
      var cId = this.clamp(id, 0, this.buttons.humModifiers.length - 1);
      this.formData.humidity = cId;
    },

    onClickRoomType(id) {
      var cId = this.clamp(id, 0, this.buttons.roomTypes.length - 1);
      this.formData.severity = cId;
    },

    onClickTemperatureModifier(id) {
      var cId = this.clamp(id, 0, this.buttons.tempModifiers.length - 1);
      this.formData.temperature = cId;
    },

    onClickTransitionModCo2(id) {
      var cId = this.clamp(id, 0, this.buttons.co2Modifiers.length - 1);
      this.formData.transitionTo.co2 = cId;
    },

    onClickTransitionModHum(id) {
      var cId = this.clamp(id, 0, this.buttons.humModifiers.length - 1);
      this.formData.transitionTo.humidity = cId;
    },

    onClickTransitionModTemp(id) {
      var cId = this.clamp(id, 0, this.buttons.tempModifiers.length - 1);
      this.formData.transitionTo.temperature = cId;
    },

    onClickTransitionTo(id) {
      var cId = this.clamp(id, -1, this.buttons.roomTypes.length - 1);
      this.formData.transitionTo.severity = cId;
    },

    onClickTransitionModVoc(id) {
      var cId = this.clamp(id, 0, this.buttons.vocModifiers.length - 1);
      this.formData.transitionTo.voc = cId;
    },

    onClickVocModifier(id) {
      var cId = this.clamp(id, 0, this.buttons.vocModifiers.length - 1);
      this.formData.voc = cId;
    }
  }
}
</script>

<style>

</style>
