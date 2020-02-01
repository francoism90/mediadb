<template lang="pug">
div(:key="data.id" class="container")
  section(class="section has-text-right")
    b-button(type="is-text" class="is-paddingless" icon-right="close" size="is-large" @click="$store.dispatch('destroyModal')")

  section(class="section")
    h1(class="title is-4") {{ data.name }}
    h2(v-if="data.relationships" class="subtitle")
      | <router-link v-if="data.relationships.user" :to="{ name: 'user-view', params: { user: data.relationships.user.id } }">{{ data.relationships.user.name }}</router-link> •
      | {{ String(data.created_at) | datestamp }}

  section(class="section")
    b-steps(v-model="step")
      b-step-item(label="Details" clickable)
        div(class="columns is-variable is-8")
          div(class="column")
            b-field(label="Name" :type="fieldType('name')" :message="firstError('tags')")
              b-input(
                type="input"
                v-model.trim="body.name"
                required
                minlength="1"
                maxlength="255"
                placeholder="Name"
              )

            b-field(label="Tags" :type="fieldType('tags')" :message="firstError('tags')")
              b-taginput(
                class="is-clearfix"
                v-model="tags"
                autocomplete
                maxtags="15"
                type="is-black"
                field="name"
                placeholder="Add tag"
                :data="tagsFiltered"
                :open-on-focus="true"
                @typing="getFilteredTags"
              )
                template(slot-scope="props")
                  <strong>{{ props.option.type }}</strong>: {{ props.option.name }}

            b-field(label="Description" :type="fieldType('description')" :message="firstError('description')")
              b-input(
                v-model.trim="body.description"
                type="textarea"
                custom-class="has-fixed-size"
                rows="5"
                minlength="0"
                maxlength="1024"
                placeholder="Markdown"
              )

          div(class="column is-4 is-hidden-mobile")
            media-info(:data="data")

      b-step-item(label="Assets" clickable)
        div(class="columns is-variable is-8")
          div(class="column")
            b-field(label="Description" :type="fieldType('description')" :message="firstError('description')")
              b-input(
                v-model.trim="body.description"
                type="textarea"
                custom-class="has-fixed-size"
                rows="5"
                minlength="0"
                maxlength="1024"
                placeholder="Markdown"
              )

          div(class="column is-4 is-hidden-mobile")
            media-info(:data="data")

      b-step-item(label="Publish" clickable)
        div(class="columns is-variable is-8")
          div(class="column")
            b-field(label="Visibility" :addons="false" :type="fieldType('visibility')" :message="firstError('visibility')")
              b-field(v-for="visibility in visibilities" :key="visibilities.key")
                b-radio(
                  v-model="body.status"
                  :native-value="visibility.key"
                  required
                ) {{ visibility.label }} - <span class="is-italic">{{ visibility.description }}</span>

            b-field(label="Danger Zone")
              b-message(type="is-danger")
                p Once you delete a video, there is no going back. Please be certain.<br><br>
                b-button(@click.prevent="remove" type="is-danger") Delete this video

          div(class="column is-4 is-hidden-mobile")
            media-info(:data="data")

      template(slot="navigation" slot-scope="{previous, next}")
        nav(class="step-navigation")
          div(class="buttons is-right")
            template(v-if="!previous.disabled")
              b-button(@click.prevent="previous.action") Back

            template(v-if="!next.disabled")
              b-button(@click.prevent="next.action") Next

            template(v-else)
              b-button(@click.prevent="update") Done
</template>

<script>
import { formHandler, tagsHandler } from '@/components/mixins'

export default {
  components: {
    MediaInfo: () => import(/* webpackChunkName: "media-info" */ '@/components/ui/MediaInfo')
  },

  mixins: [formHandler, tagsHandler],

  props: {
    id: {
      type: String,
      required: true
    }
  },

  data () {
    return {
      data: {},
      meta: {},
      body: {},
      extract: [
        'id',
        'name',
        'description',
        'status'
      ],
      step: 0
    }
  },

  computed: {
    visibilities () {
      return this.$store.state.media.visibilities || []
    },

    userRoute () {
      return {
        name: 'user-view',
        params: { user: this.data.relationships.user.id }
      }
    },

    tags: {
      get () {
        return this.tagsSelected
      },

      set (value) {
        this.setSelectedTags(value)
      }
    }
  },

  async created () {
    const { data = {}, meta = {} } = await this.$store.dispatch('media/find', this.id)

    this.$set(this, 'data', data)
    this.$set(this, 'meta', meta)

    // Values that may be overwritten (except 'id')
    this.extract.forEach((key) => {
      this.body[key] = this.data[key]
    })

    this.setSelectedTags(data.relationships.tags)
  },

  methods: {
    async update () {
      // Attach tags model
      this.body.tags = this.tags

      const { success } = await this.submit('media/update', this.body)

      if (success) {
        await this.$store.dispatch('media/get', this.data.id)
        await this.$store.dispatch('destroyModal')
      }
    },

    remove () {
      this.$buefy.dialog.confirm({
        title: this.data.name,
        message: 'Are you sure you want to delete this video?',
        type: 'is-danger',
        onConfirm: async () => {
          await this.$store.dispatch('media/delete', this.data.id)

          await this.$router.push('/', () => {}) // #2932
          await this.$store.dispatch('destroyModal')
        }
      })
    }
  }
}
</script>
