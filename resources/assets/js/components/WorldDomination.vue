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
      <input type="hidden" name="h" :value="formData.humidity">
      <input type="hidden" name="s" :value="formData.severity">
      <input type="hidden" name="t" :value="formData.temperature">
      <input type="hidden" name="th" :value="formData.transitionTo.humidity">
      <input type="hidden" name="ts" :value="formData.transitionTo.severity">
      <input type="hidden" name="tt" :value="formData.transitionTo.temperature">
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
        ]
      },
      csrfToken: '',
      formData: {
        humidity: 1,
        severity: 0,
        temperature: 1,
        transitionTo: {
          humidity: 1,
          severity: -1,
          temperature: 1
        }
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

    onClickHumidityModifier(id) {
      var cId = this.clamp(id, 0, this.buttons.humModifiers.length);
      this.formData.humidity = cId;
    },

    onClickRoomType(id) {
      var cId = this.clamp(id, 0, this.buttons.roomTypes.length);
      this.formData.severity = cId;
    },

    onClickTemperatureModifier(id) {
      var cId = this.clamp(id, 0, this.buttons.tempModifiers.length);
      this.formData.temperature = cId;
    },

    onClickTransitionModHum(id) {
      var cId = this.clamp(id, 0, this.buttons.humModifiers.length);
      this.formData.transitionTo.humidity = cId;
    },

    onClickTransitionModTemp(id) {
      var cId = this.clamp(id, 0, this.buttons.tempModifiers.length);
      this.formData.transitionTo.temperature = cId;
    },

    onClickTransitionTo(id) {
      var cId = this.clamp(id, -1, this.buttons.roomTypes.length);
      this.formData.transitionTo.severity = cId;
    }
  }
}
</script>

<style>

</style>
