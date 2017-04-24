<template>
    <div class="dashboard">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th @click="sort('status')">Status</th>
                        <th></th>
                        <th @click="sort('name')">Name</th>
                        <th @click="sort('uploadRatio')">Ratio</th>
                        <th @click="sort('uploadRate')"><i class="fa fa-fw fa-arrow-up"></i> Rate</th>
                        <th @click="sort('downloadRate')"><i class="fa fa-fw fa-arrow-down"></i> Rate</th>
                        <th @click="sort('percentDone')">Completion %</th>
                        <th @click="sort('eta')">ETA</th>
                        <th @click="sort('unixStartTime')">Start Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="t in torrents | orderBy sortKey order sortOrder | filterBy filter" track-by="hash">
                        <td>
                            <i class="fa fa-fw {{ getStatusIcon(t.status) }}" alt="{{ getStatus(t.status) }}"></i>
                        </td>
                        <td>
                            <button v-if="t.status == 0" class="btn btn-xs btn-default" @click="startTorrent(t.hash)">
                                <i class="fa fa-fw fa-play"></i>
                            </button>
                            <button v-else class="btn btn-xs btn-default" @click="stopTorrent(t.hash)">
                                <i class="fa fa-fw fa-pause"></i>
                            </button>
                            <button class="btn btn-xs btn-default" @click="deleteTorrent(t)">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                            <button class="btn btn-xs btn-default" @click="getTorrentInfo(t)">
                                <i class="fa fa-fw fa-info-circle"></i>
                            </button>
                        </td>
                        <td>{{t.name}}</td>
                        <td>{{ Math.round(t.uploadRatio * 100) / 100 }}</td>
                        <td>{{ t.uploadRate }}</td>
                        <td>{{ t.downloadRate }}</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" v-bind:class="getProgressColor(t.status)" role="progressbar" aria-valuenow="{{t.percentDone}}" aria-valuemin="0" aria-valuemax="100" v-bind:style="{width: t.percentDone + '%'}">
                                    {{t.percentDone}}%
                                </div>
                            </div>
                        </td>
                        <td>{{t.eta}}</td>
                        <td>{{t.startDate}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <torrent-modal :torrent="selectedTorrent"></torrent-modal>
</template>

<style>
    .dashboard .list-group{
        margin-bottom: 5px;
    }
    .dashboard .badge{
        background-color: #8eb4cb;
    }
    .dashboard .progress {
        margin-bottom: 0px;
    }
    .dashboard{
        font-size: 12px;
    }
</style>

<script>
    import TorrentModal from './torrent-modal.vue'
    export default {
        props:{
            filter: null,
            counts: {}
        },
        data() {
            return {
                torrents: [],
                sortKey: 'status',
                sortOrder: 0,
                selectedTorrent: null
            }
        },
        components: {
            TorrentModal
        },
        ready() {
            var vThis = this;
            vThis.getTorrents();
            setInterval(function() {
                vThis.getTorrents();
            }, 10000);
        },
        computed: {
            paused: function(){
                return this.torrents.filter(function(item){
                    return item.status == 0;
                }).length;
            },
            seeding: function(){
                return this.torrents.filter(function(item){
                    return item.status == 6;
                }).length;
            }
        },
        watch: {
            torrents: function(oldVal, newVal){
                this.counts.paused = this.paused;
                this.counts.seeding = this.seeding;
            }
        },
        methods: {
            sort: function(newSortKey){
                if( this.sortKey == newSortKey ){
                    if( this.sortOrder == 0 ){
                        this.sortOrder = -1;
                    }
                    else{
                        this.sortOrder = 0;
                    }
                }
                else{
                    this.sortKey = newSortKey;
                }
            },
            getTorrentInfo: function(torrent){
                this.selectedTorrent = torrent;
                $('#torrent-info').modal('show');
            },
            getTorrents: function(){
                this.$http.get('/api/torrents')
                    .then(response => {
                        this.torrents = response.data;
                    });
            },
            startTorrent: function(hash){
                this.$http.post('/api/torrents/' + hash + '/start')
                    .then(response => {
                        this.getTorrents();
                    });
            },
            stopTorrent: function(hash){
                this.$http.post('/api/torrents/' + hash + '/stop')
                    .then(response => {
                        this.getTorrents();
                    });
            },
            deleteTorrent: function(torrent){
                this.torrents.$remove(torrent);
                this.$http.delete('/api/torrents/' + torrent.hash)
                    .then(response => {
                        this.getTorrents();
                    });
            },
            getStatus: function(status){
                var statuses = ["Stopped", "Check waiting", "Checking", "Download waiting", "Downloading", "Seed waiting", "Seeding"];
                return statuses[status];
            },
            getStatusIcon: function(status){
                var statuses = ["fa-pause", "Check waiting", "fa-spinner fa-spin", "Download waiting", "fa-download", "Seed waiting", "fa-upload"];
                return statuses[status];
            },
            getProgressColor: function(status){
                var classes = [
                    'progress-bar-warning',
                    'progress-bar-info',
                    'progress-bar-info',
                    'progress-bar-striped active',
                    'progress-bar-striped active',
                    'progress-bar-success',
                    'progress-bar-success'
                ];
                return classes[status];
            }
        }
    }
</script>
