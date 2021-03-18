<template>
 <div>
    <v-alert
      icon="mdi-devices"
      prominent
      text
      type="info"
      class="d-sm-none"
    >
    The Inventory feature works better on a large screen.   
    </v-alert>
    <v-toolbar
        flat
        dark
        color="primary"
    >
        <v-toolbar-title>Inbox <small>uconn</small></v-toolbar-title>
        
        <v-spacer></v-spacer>

        <v-btn
            dark
            color="secondary">
            <v-icon>mdi-export</v-icon>
            export
        </v-btn>    

        <v-menu
        bottom
        left
        >
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                dark
                icon
                v-bind="attrs"
                v-on="on"
                >
                <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
            </template>

            <v-list>
                <v-list-item>
                    <v-list-item-title>Inbox</v-list-item-title>
                </v-list-item>

                <v-list-item>
                    <v-list-item-title>Archived Items</v-list-item-title>
                </v-list-item>

                <v-list-item>
                    <v-list-item-title>Flagged Items</v-list-item-title>
                </v-list-item>


                <v-list-item>
                    <v-list-item-title>Saved Snapshots</v-list-item-title>
                </v-list-item>                    
            </v-list>
        </v-menu>

    </v-toolbar>
 <v-data-table
    :headers="headers"
    :items="items"
    :items-per-page="15"
    class="elevation-1"
  >
    
    <template v-slot:[`item.rank`]="{ item }">
      <v-chip
        :color="item.rank"
        dark
      >
        {{ item.rank }}
      </v-chip>
    </template>

    <template v-slot:[`item.actions`]="{ item }">
      <v-icon
        small
        @click="deleteItem(item)"
      >
        mdi-delete
      </v-icon>
    </template>
  
  </v-data-table>
 </div>
</template>

<script>
// @ is an alias to /src


export default {
  name: 'Inventory',
  data () {
      return {
        headers: [
          {
            text: 'Name',
            align: 'start',
            sortable: false,
            value: 'name',
          },
          { text: 'UPC', value: 'upc' },
          { text: 'Rank', value: 'rank' },
          { text: 'Date', value:'datestamp'},
          { text: 'BlameID', value: 'blameID' },
          { text: 'Actions', value:'actions', sortable: false},
        ],
        items: [
          {
            name: 'Frozen Yogurt',
            upc: "159",
            rank: "rarely",
            blameID:"joel.salisbury@gmail.com",
            datestamp:Date() 
          },
          {
            name: 'Ice cream sandwich',
            upc: "237",
            rank: "often",
            blameID:"joel.salisbury@gmail.com",
            datestamp:Date()
          },
          {
            name: 'Eclair',
            upc: "262",
            rank: "sometimes",
            blameID:"joel.salisbury@gmail.com",
            datestamp:Date()
          },
        ],
      }
    },
}
</script>
