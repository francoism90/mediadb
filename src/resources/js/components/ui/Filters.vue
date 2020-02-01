<template lang="pug">
section(:key="moduleId" class="filters")
  nav(class="level")
    div(class="level-left")
      div(class="level-item")
          b-dropdown(v-model="filterSort" aria-role="list")
            b-button(
              slot="trigger"
              type="is-text"
              size="is-normal"
              :class="{ 'is-active': hasFilterSort }"
              icon-right="chevron-down"
            ) {{ filterSortLabel }}

            b-dropdown-item(
              v-for="sorter in sorters"
              :key="sorter.key"
              :value="sorter.key"
              aria-role="listitem"
            ) {{ sorter.label }}

          b-button(v-if="hasFilterSort || hasFilterTags" class="is-hidden-desktop" type="is-text" size="is-normal" icon-right="filter-remove" @click="resetFilters()")
          b-button(class="is-hidden-desktop" type="is-text" size="is-normal" :disabled="!reloadable" icon-right="refresh" @click="reload()")

      div(class="level-item")
        b-dropdown(
          v-for="tagger in taggers"
          :key="tagger.key"
          v-model="filterTags"
          multiple
          aria-role="list"
        )
          b-button(
            v-if="getTagsByType(tagger.key).length"
            slot="trigger"
            type="is-text"
            size="is-normal"
            :class="{ 'is-active': getTagsOfType(tagger.key, filterTags).length }"
            icon-right="chevron-down"
          ) {{ tagger.label }}

          b-dropdown-item(
            v-for="tag in getTagsByType(tagger.key)"
            :key="tag.id"
            :value="tag.id"
            aria-role="listitem"
          ) {{ tag.name }}

    div(class="level-right is-hidden-touch")
      div(class="level-item")
        b-button(v-if="hasFilterSort || hasFilterTags" type="is-text" size="is-normal" icon-right="filter-remove" @click="resetFilters()")
        b-button(type="is-text" size="is-normal" :disabled="!reloadable" icon-right="refresh" @click="reload()")
</template>

<script>
import { filtersHandler, tagsHandler } from '@/components/mixins'

export default {
  timers: {
    reloadTimer: { time: 9000, autostart: true }
  },

  mixins: [filtersHandler, tagsHandler],

  data () {
    return {
      reloadable: false
    }
  },

  methods: {
    reloadTimer () {
      this.reloadable = true
    },

    reload () {
      this.$timer.start('reloadTimer')
      this.reloadable = false
      this.resetPaginate({ id: this.moduleId, props: {} })
    }
  }
}
</script>
