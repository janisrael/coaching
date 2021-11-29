<template>
  <div>
    <svg viewBox='0 0 105 105' :fill='icon_color'>
      <circle cx='12.5' cy='12.5' r='12.5'>
        <animate attributeName='fill-opacity' begin='0s' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='12.5' cy='52.5' r='12.5' fill-opacity='.5'><animate attributeName='fill-opacity' begin='100ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='52.5' cy='12.5' r='12.5'><animate attributeName='fill-opacity' begin='300ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='52.5' cy='52.5' r='12.5'><animate attributeName='fill-opacity' begin='600ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='92.5' cy='12.5' r='12.5'><animate attributeName='fill-opacity' begin='800ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='92.5' cy='52.5' r='12.5'><animate attributeName='fill-opacity' begin='400ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='12.5' cy='92.5' r='12.5'><animate attributeName='fill-opacity' begin='700ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='52.5' cy='92.5' r='12.5'><animate attributeName='fill-opacity' begin='500ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/></circle><circle cx='92.5' cy='92.5' r='12.5'><animate attributeName='fill-opacity' begin='200ms' dur='1s' values='1;.2;1' calcMode='linear' repeatCount='indefinite'/>
      </circle>
    </svg>
  </div>
</template>

<script>
export default {
  name: 'ScLoader',
  props: {
    color: {
      required: false,
      type: String
    }
  },
  data() {
    return {
      icon_color: '#ffffff'
    }
  },
  created() {
    this.getColor()
  },
  methods: {
    getColor() {
      if(this.color !== null || this.color !== '') {
        switch (this.color) {
          case "red":
            this.icon_color = '#e00202'
            break;
          case "blue":
            this.icon_color = '#003CD6'
            break;
          case "white":
            this.icon_color = '#ffffff'
            break;
          case "black":
            this.icon_color = '#000000'
            break;
          default:
            this.icon_color = '#ffffff'
        }
      }
    }
  }
//   classes:
//   'main-loader' = whole page icon loader(size: 100),
//   'window-loader' = component/window icon loader(size: 30),
//   'button-loader' = button icon loader(size: 12)

//   Acceptable Attributes:
//   'color': 'white','black,'red','blue','default: white'
//
}
</script>

<style scoped>

</style>
