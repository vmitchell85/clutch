<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Transmission\Client;
use Transmission\Transmission;

class TransmissionController extends Controller
{
    public function index(){
        return collect(self::getTorrents())->map(function($torrent){
            return collect($torrent)->except('peers', 'trackers', 'trackerStats');
        })->each(function($torrent){
            $torrent['unixStartTime'] = Carbon::parse($torrent['startDate'])->toDateTimeString();
        });
    }

    protected function getTorrents()
    {
        $transmission = self::getClient();
        return collect($transmission->all())->map(function($torrent){
            return self::parseTorrent($torrent);
        });
    }

    public function start($hash)
    {
        $transmission = self::getClient();
        $torrent = $transmission->get($hash);
        $transmission->start($torrent);

        return [
            'status' => 'starting'
        ];
    }

    public function stop($hash)
    {
        $transmission = self::getClient();
        $torrent = $transmission->get($hash);
        $transmission->stop($torrent);

        return [
            'status' => 'stopping'
        ];
    }

    public function destroy($hash)
    {
        $transmission = self::getClient();
        $torrent = $transmission->get($hash);
        $transmission->remove($torrent);

        return [
            'status' => 'deleting'
        ];
    }

    protected function parseTorrent($torrent)
    {
        return [
            'id' => $torrent->getId(),
            'eta' => self::parseETA( $torrent->getEta() ),
            'size' => self::parseSize($torrent->getSize()),
            'name' => $torrent->getName(),
            'hash' => $torrent->getHash(),
            'status' => $torrent->getStatus(),
            'finished' => $torrent->isFinished(),
            'startDate' => self::parseDate($torrent->getStartDate()),
            'diffStartDate' => self::parseDate($torrent->getStartDate(), true),
            'uploadRate' => self::parseSpeed($torrent->getUploadRate()),
            'downloadRate' => self::parseSpeed($torrent->getDownloadRate()),
            'peersConnected' => $torrent->getPeersConnected(),
            'percentDone' => $torrent->getPercentDone(),
            'files' => collect($torrent->getFiles())->map(function($file){
                return self::parseFile($file);
            }),
            'peers' => $torrent->getPeers(),
            'trackers' => $torrent->getTrackers(),
            'trackerStats' => $torrent->getTrackerStats(),
            'uploadRatio' => $torrent->getUploadRatio(),
            'downloadDir' => $torrent->getDownloadDir(),
            'downloadedEver' => $torrent->getDownloadedEver(),
            'uploadedEver' => $torrent->getUploadedEver(),
        ];
    }

    protected function parseSize($size)
    {
        if ( $size <= 0 ){ return null; }
        $labels = ['B', 'KB', 'MB', 'GB'];
        $count = 0;
        while( $size > 1024 ){
            $count++;
            $size = $size/1024;
        }

        return round($size, 2).' '.$labels[$count];
    }

    protected function parseSpeed($speed)
    {
        if( $speed <= 0 ){
            return null;
        }
        $labels = ['B/s', 'kB/s', 'MB/s'];
        $count = 0;
        while($speed > 1024){
            $count++;
            $speed = $speed/1024;
        }

        return round($speed) . ' ' . $labels[$count];
    }

    protected function parseDate($timestamp, $return_diff=false)
    {
        if ( $return_diff ){
            
            return \Carbon\Carbon::createFromTimestamp($timestamp)->diffForHumans();
        }
        
        return \Carbon\Carbon::createFromTimestamp($timestamp)->toDayDateTimeString();
    }

    protected function parseETA($eta)
    {
        if( $eta < 0 ){
            return null;
        }
        return \Carbon\Carbon::now()->addSeconds($eta)->diffForHumans();
    }

    protected function parseFile($file)
    {
        return [
            'name' => $file->getName(),
            'size' => $file->getSize(),
            'completed' => $file->getCompleted(),
        ];
    }

    protected function getClient()
    {
        $client = new Client(env('TRANSMISSION_BASE_URL'), env('TRANSMISSION_PORT', 9091), env('TRANSMISSION_PATH', '/transmission/rpc'));
        if( env('TRANSMISSION_USERNAME') ){
            $client->authenticate(env('TRANSMISSION_USERNAME'), env('TRANSMISSION_PASSWORD'));
        }
        $transmission = new Transmission();
        $transmission->setClient($client);

        return $transmission;
    }
}
