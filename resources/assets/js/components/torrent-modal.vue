<template>
    <div class="modal fade" id="torrent-info">
        <div class="modal-dialog" style="width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Torrent Info</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table v-if="torrent" class="table table-hover table-bordered">
                            <tbody>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ torrent.name }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{{ getStatus(torrent.status) }}</td>
                                </tr>
                                <tr>
                                    <th>Size</th>
                                    <td>{{ torrent.size }}</td>
                                </tr>
                                <tr>
                                    <th>Ratio</th>
                                    <td>{{ Math.round(torrent.uploadRatio * 100) / 100 }}</td>
                                </tr>
                                <tr>
                                    <th>Upload Rate</th>
                                    <td>{{ torrent.uploadRate }}</td>
                                </tr>
                                <tr>
                                    <th>Download Rate</th>
                                    <td>{{ torrent.downloadRate }}</td>
                                </tr>
                                <tr>
                                    <th>% Complete</th>
                                    <td>{{ torrent.percentDone }}%</td>
                                </tr>
                                <tr>
                                    <th>Date Started</th>
                                    <td>
                                        {{ torrent.diffStartDate }}<br>
                                        <small>({{ torrent.startDate }})</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Files</th>
                                    <td>
                                        <ul>
                                            <li v-for="file in torrent.files">{{ file.name }}</li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            torrent: null
        },
        methods: {
            getStatus: function(status){
                var statuses = ["Paused", "Check waiting", "Checking", "Download waiting", "Downloading", "Seed waiting", "Seeding"];
                return statuses[status];
            }
        }
    }
</script>