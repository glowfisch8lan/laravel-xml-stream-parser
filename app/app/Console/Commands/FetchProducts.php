<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Prewk\XmlStringStreamer;
use Prewk\XmlStringStreamer\Stream;
use Prewk\XmlStringStreamer\Parser;

class FetchProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-products {--uuid= : UUID xml file to parse} {--url= : URL to xml file Quarta Hunt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download xml from url, save, then parse and convert to structure CSV';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {

        $filesPath = storage_path('app/files');
        $folderXml = 'xml';
        $folderCsv = 'csv';
        $fileNameXml = sprintf('%s.xml', $this->option('uuid') ?? Str::uuid());
        $fileNameCsv = sprintf('%s.csv', Str::uuid()->toString());
        $filePathCsv = sprintf('%s/%s/%s', $filesPath, $folderCsv, $fileNameCsv);
        $filePathXml = sprintf('%s/%s/%s', $filesPath, $folderXml, $fileNameXml);

        $url = $this->option('url');

        if ($url) {
            $fileContentXml = Http::get($url);
            Storage::disk('local')->put($filePathXml, $fileContentXml);
            $this->info(sprintf('File %s successful downloaded', $fileNameXml));
        }


        $totalSize = filesize($filePathXml);
        $stream = new Stream\File($filePathXml, 1024, fn($chunk, $readBytes) => $this->info("Progress: $readBytes / $totalSize\n"));
        $parser = new Parser\StringWalker(["captureDepth" => 3]);
        $streamer = new XmlStringStreamer($parser, $stream);
        $fileCsvStream = fopen($filePathCsv, 'a');
        if (!$fileCsvStream) {
            throw new \DomainException(sprintf('Sorry, cannot open stream %s!', $fileNameCsv));
        }

        $headers = [
            'id',
            'name',
            'url',
            'price',
            'picture',
            'available',
        ];
        fputcsv($fileCsvStream, $headers, ';');
        while ($node = $streamer->getNode()) {
            $simpleXmlNode = simplexml_load_string($node);
            $items = ((array)$simpleXmlNode)['offer'] ?? null;

            if (!is_null($items)) {
                array_map(function ($item) use ($fileCsvStream) {
                    $item = (array) $item;
                    $itemCandidate = [
                        'id' => $item['@attributes']['id'],
                        'name' => $item['name'],
                        'url' => $item['url'],
                        'price' => $item['price'],
                        'picture' => $item['picture'] ?? null,
                        'available' => $item['@attributes']['available'],
                    ];
                    fputcsv($fileCsvStream, $itemCandidate, ';');
                }, $items);

            }
        }


        $this->info(sprintf('%s converted!', $filePathCsv));
        fclose($fileCsvStream);
    }
}
