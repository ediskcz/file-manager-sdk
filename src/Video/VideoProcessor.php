<?php /** @noinspection ALL */

namespace Edisk\FileManager\Video;

use InvalidArgumentException;

final class VideoProcessor
{
    public const STREAM_VIDEO = 'video';
    public const STREAM_AUDIO = 'audio';
    public const STREAM_SUBTITLES = 'subtitles';

    private array $streams;
    private array $probe_data_formatted;

    private array $params = [
        self::STREAM_VIDEO => [
            'duration' => null,
            'resolution' => null,
            'codec' => null,
            'bitrate' => null,
            'frequency' => null,
        ],
        self::STREAM_AUDIO => [
            'codec' => null,
            'bitrate' => null,
            'channels' => null,
        ],
        self::STREAM_SUBTITLES => [

        ],
    ];

    private string $ffmpegPath = '/usr/bin';

    private ?int $audioStreamIndex = null;
    private ?int $subtitle_stream_index = null;
    private int $subtitle_frames = 0;

    public function __construct(private string $sourcePath)
    {
    }

    /**
     * @throws VideoProcessorException
     */
    private function loadStreams(): void
    {
        if (null === $this->sourcePath && !file_exists($this->sourcePath)) {
            throw new VideoProcessorException('File deleted, unable to load streams');
        }

        $cmd = 'ffprobe';
        $cmd .= ' -print_format json -v error -show_entries stream=nb_read_frames -show_format -show_streams';
        $cmd .= ' ' . $this->sourcePath;

        $results = [];
        exec($cmd, $results);

        $data = json_decode(implode($results), true);

        $videoStream = $audioStream = $subtitleStream = null;
        $audio_stream_index = $subtitle_stream_index = 0;
        foreach ((array) $data['streams'] as $stream) {
            if (!$videoStream && isset($stream['codec_type']) && $stream['codec_type'] === 'video') {
                $videoStream = $stream;
            }
            if (!$audioStream && isset($stream['codec_type']) && $stream['codec_type'] === 'audio') {
                $audioStream = $stream;
            }
            if (!$subtitleStream && isset($stream['codec_type']) && $stream['codec_type'] === 'subtitle') {
                $subtitleStream = $stream;
            }
        }
        if (!empty($data['format'])) {
            $this->probe_data_formatted = (array) $data['format'];
        }
        $audio_lang_priority = $subtitle_lang_priority = (array) ConfigHelper::load('convert.lang_priority', []);
        $lang_types = (array) ConfigHelper::load('convert.lang_parse_types', []);
        if (!empty($this->file->audio_lang)) {
            array_unshift($audio_lang_priority, $this->file->audio_lang);
        }

        // find priority audio stream if exists
        $priority_audio_found = false;
        foreach ($audio_lang_priority as $lang) {
            $types = $lang_types[$lang];
            $audio_index = 0;
            foreach ((array) $data['streams'] as $stream) {
                if (isset($stream['codec_type']) && $stream['codec_type'] === 'audio') {
                    if (isset($stream['tags']['language'])
                        && in_array($stream['tags']['language'], $types, true)) {
                        $audioStream = $stream;
                        $audio_stream_index = $audio_index;
                        $priority_audio_found = true;
                        break;
                    }
                    $audio_index++;
                }
            }
            if ($priority_audio_found) {
                break;
            }
        }

        // find subtitle stream if exists
        $priority_subtitles_found = false;
        foreach ($subtitle_lang_priority as $lang) {
            $types = $lang_types[$lang];
            $subtitle_index = 0;
            foreach ((array) $data['streams'] as $stream) {
                if (isset($stream['codec_type']) && $stream['codec_type'] === 'subtitle') {
                    if (isset($stream['tags']['language'])
                        && in_array($stream['tags']['language'], $types, true)) {
                        $subtitle_frames = (int) ($stream['tags']['NUMBER_OF_FRAMES'] ?? 0);
                        if ($subtitle_frames >= $this->subtitle_frames) {
                            $subtitleStream = $stream;
                            $subtitle_stream_index = $subtitle_index;
                            $priority_subtitles_found = true;
                            $this->subtitle_frames = $subtitle_frames;
                        }
                    }
                    $subtitle_index++;
                }
            }
            if ($priority_subtitles_found) {
                break;
            }
        }

        if (empty($videoStream)) {
            throw new VideoProcessorException('Unable to load video streams, processing failed');
        }

        $this->streams = [
            self::STREAM_VIDEO => $videoStream,
            self::STREAM_AUDIO => $audioStream,
            self::STREAM_SUBTITLES => $subtitleStream,
        ];
        $this->audioStreamIndex = $audio_stream_index;
        $this->subtitle_stream_index = $subtitle_stream_index;

        $this->load_params();
    }

    /**
     * @param bool $reload
     * @return null|array
     * @throws VideoProcessorException
     */
    public function get_streams(bool $reload = false): ?array
    {
        if (empty($this->streams) || $reload) {
            $this->loadStreams();
        }

        return $this->streams;
    }

    /**
     * @param string $type
     * @param bool $reload
     * @return array|null
     * @throws VideoProcessorException
     */
    public function getStream(string $type, bool $reload = false): ?array
    {
        if ($type && !in_array($type, [self::STREAM_VIDEO, self::STREAM_AUDIO, self::STREAM_SUBTITLES], true)) {
            throw new InvalidArgumentException('Invalid stream type');
        }
        if (empty($this->streams) || $reload) {
            $this->loadStreams();
        }

        return $this->streams[$type] ?? null;
    }

    /**
     * @throws VideoProcessorException
     */
    private function load_params(): void
    {
        $video_stream = $this->getStream(self::STREAM_VIDEO);
        $audio_stream = $this->getStream(self::STREAM_AUDIO);
        $length = $this->get_stream_duration($video_stream);
        $duration = $length['hours'] . 'h' . ' ' . $length['minutes'] . 'm ' . $length['seconds'] . 's';
        if ($length['seconds_total']) {
            $this->params[self::STREAM_VIDEO]['movie_duration'] = $duration;
        }
        $movie_resolution = $video_stream['width'] . '*' . $video_stream['height'];
        $this->params[self::STREAM_VIDEO]['movie_resolution'] = $movie_resolution;
        if ($movie_resolution) {
            $resolution_array = explode('*', $movie_resolution);
            if (count($resolution_array) === 2 && $resolution_array[0] >= 1280 && $resolution_array[1] >= 720) {
                $this->params[self::STREAM_VIDEO]['hd'] = 1;
            }
        }
        $this->params[self::STREAM_VIDEO]['movie_codec'] = $video_stream['codec_name'] ?? '';
        $this->params[self::STREAM_VIDEO]['movie_bitrate'] = $video_stream['bit_rate'] ?? '';
        $this->params[self::STREAM_VIDEO]['movie_freq'] = $video_stream['r_frame_rate'] ?? '';
        $frequency = $this->params[self::STREAM_VIDEO]['movie_freq'];
        if ($frequency && substr_count($frequency, '/') + 1 === 2) {
            $parts = explode('/', $frequency);
            $frames = (int) $parts[0] / (int) $parts[1];
            if ($frames > 60) {
                $this->params[self::STREAM_VIDEO]['movie_freq'] = '60/1';
            }
        }
        if ($audio_stream) {
            $this->params[self::STREAM_AUDIO]['movie_audio_codec'] = $audio_stream['codec_long_name'] ?? '';
            $this->params[self::STREAM_AUDIO]['movie_audio_bitrate'] = $audio_stream['bit_rate'] ?? '';
            $this->params[self::STREAM_AUDIO]['movie_audio_channels'] = $audio_stream['channels'] ?? '';
        }
    }

    /**
     * @param array $stream
     * @return array ['hours' => $h, 'minutes' => $m, 'seconds' => $s, 'seconds_total' => $secs]
     */
    private function get_stream_duration(array $stream): array
    {
        $secs = !empty($stream['duration']) ? (float) $stream['duration'] : 0;
        if (!$secs && !empty($this->probe_data_formatted['duration'])) {
            $secs = (float) $this->probe_data_formatted['duration'];
        }
        if (!$secs && !empty($stream['tags'])) {
            foreach (array_keys($stream['tags']) as $key) {
                if (str_starts_with($key, 'DURATION')) {
                    $time = $stream['tags'][$key];
                    $hours = $minutes = $seconds = 0;
                    sscanf($time, '%d:%d:%d', $hours, $minutes, $seconds);
                    $secs = $hours * 3600 + $minutes * 60 + $seconds;
                }
                if ($secs) {
                    break;
                }
            }
        }

        return VideoMeta::parseDuration($secs);
    }

    /**
     * TODO generate multiple previews, use $count
     * @throws VideoProcessorException
     */
    public function generate_previews($target_dir, int $count = 1): int
    {
        $video_stream = $this->getStream(self::STREAM_VIDEO);
        $duration = $this->get_stream_duration($video_stream);
        if ($duration['duration'] <= 0) {
            throw new VideoProcessorException('Unable to generate preview, video length invalid');
        }

        $pic_capture_seconds = $duration['duration'] / 2;
        $hours = sprintf('%02d', floor($pic_capture_seconds / 3600));
        $minutes = sprintf('%02d', floor(($pic_capture_seconds / 60) % 60));
        $seconds = sprintf('%02d', $pic_capture_seconds % 60);
        $cmd = 'nice -n 15 /ffmpeg -y ';
        $cmd .= '-ss ' . $hours . ':' . $minutes . ':' . $seconds . ' ';
        $cmd .= '-i ' . $this->sourcePath . ' ';
        $cmd .= '-vframes 1 ';
        $cmd .= $target_dir . DIRECTORY_SEPARATOR . '1_big.jpg';
        exec($cmd);
        Preview::generate_thumbs($target_dir);

        return $count;
    }

    /**
     * Checks if source file is already in mp4 format which can be player via HTML5 video player
     *
     * @return bool
     * @throws VideoProcessorException
     */
    public function is_source_playable(): bool
    {
        $result = true;
        $result &= ($this->params[self::STREAM_VIDEO]['movie_codec'] === 'h264');
        $result &= ($this->file->get_extension() === 'mp4');

        $audio_stream = $this->getStream(self::STREAM_AUDIO);
        if ($audio_stream) {
            $result &= (0 === stripos($this->params[self::STREAM_AUDIO]['movie_audio_codec'], 'aac'));
        }

        return $result;
    }

    public function target_exists(): bool
    {
        return file_exists($this->target);
    }

    /**
     * @throws VideoProcessorException
     * @noinspection SpellCheckingInspection
     */
    public function getConvertCmd(string $targetPath): string
    {
        $cmd = 'nice -n 15 ffmpeg -i ' . $this->sourcePath;
        $cmd .= ' -vcodec libx264';
        $cmd .= ' -acodec libfdk_aac';
        // $cmd .= ' -acodec aac';
        $cmd .= ' -strict experimental';
        $cmd .= ' -pix_fmt yuv420p';
        $cmd .= ' -profile:v baseline';
        if ($this->params[self::STREAM_VIDEO]['movie_freq'] === '60/1') {
            $cmd .= ' -r 60/1';
        }
        $cmd .= ' -map 0:v:0';
        $audio_stream = $this->getStream(self::STREAM_AUDIO);
        if ($audio_stream) {
            $cmd .= ' -map 0:a:' . $this->audioStreamIndex;
        }
        $cmd .= ' -f mp4 -y ' . $targetPath;

        return $cmd;
    }

    /**
     * @throws VideoProcessorException
     */
    public function convert(string $targetPath): bool
    {
        if (!file_exists($this->sourcePath)) {
            throw new InvalidArgumentException('Invalid file source');
        }

        exec($this->getConvertCmd($targetPath));

        return (file_exists($targetPath) && filesize($targetPath) > 0);

        if (file_exists($this->target) && filesize($this->target) > 0) {
            $this->file->set_converted();
            if ($this->file->ext !== 'mp4') {
                $this->file->filename = str_replace('.' . $this->file->ext, '.mp4', $this->file->filename);
            }
            $this->file->ext = 'mp4';
            $this->file->filesize = filesize($this->target);

            return true;
        }

        return false;
    }

    /**
     * Extract .srt subtitles if encoded subtitles available
     *
     * @throws VideoProcessorException
     */
    public function extractSubtitles(string $targetPath): bool
    {
        // extract subtitles from stream to file
        $stream = $this->getStream(self::STREAM_SUBTITLES);
        if (!$stream) {
            return false;
        }

        $cmd = $this->ffmpegPath . '/ffmpeg -i ' . $this->sourcePath;
        $cmd .= ' -an -vn';
        $cmd .= ' -map 0:s:' . $this->subtitle_stream_index . ' -c:s:0';
        $cmd .= ' srt ' . $targetPath;

        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        exec($cmd);

        if (file_exists($targetPath)) {
            $data = strip_tags(file_get_contents($targetPath));
            $data = str_replace("\r", '', $data);
            file_put_contents($targetPath, $data);

            return true;
        }

        return false;
    }

    public function store(): void
    {
        $this->file->set_processed();
        foreach ($this->params[self::STREAM_VIDEO] as $key => $val) {
            $this->file->$key = $val;
        }
        foreach ($this->params[self::STREAM_AUDIO] as $key => $val) {
            $this->file->$key = $val;
        }
        $this->file->save();
    }
}
