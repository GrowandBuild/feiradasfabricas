<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    public function index()
    {
        $videos = $this->getVideosFromStorage();
        return response()->json($videos);
    }
    
    public function upload(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,mov,avi,wmv,flv,webm|max:100000', // 100MB
            'title' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500'
        ]);
        
        try {
            $file = $request->file('video');
            $title = $request->title ?: Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $description = $request->description;
            
            // Generate unique filename
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Create directories if they don't exist
            $videoDir = storage_path('app/public/videos');
            $metadataDir = storage_path('app/public/videos/metadata');
            $thumbnailDir = public_path('storage/videos/thumbnails');
            
            if (!File::exists($videoDir)) {
                File::makeDirectory($videoDir, 0755, true);
            }
            if (!File::exists($metadataDir)) {
                File::makeDirectory($metadataDir, 0755, true);
            }
            if (!File::exists($thumbnailDir)) {
                File::makeDirectory($thumbnailDir, 0755, true);
            }
            
            // Store video file
            $path = $file->storeAs('videos', $filename, 'public');
            
            // Create metadata file
            $metadata = [
                'title' => $title,
                'description' => $description,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now()->toISOString()
            ];
            
            $metadataPath = $metadataDir . '/' . $filename . '.json';
            File::put($metadataPath, json_encode($metadata, JSON_PRETTY_PRINT));
            
            // Generate thumbnail
            $thumbnailUrl = $this->generateThumbnail($filename);
            
            return response()->json([
                'success' => true,
                'message' => 'Vídeo enviado com sucesso!',
                'video' => [
                    'id' => $filename,
                    'title' => $title,
                    'description' => $description,
                    'url' => asset('storage/' . $path),
                    'thumbnail' => $thumbnailUrl,
                    'size' => $this->formatFileSize($file->getSize()),
                    'uploaded_at' => $metadata['uploaded_at']
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Video upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar vídeo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function delete($filename)
    {
        try {
            // Delete video file
            $videoPath = public_path("storage/videos/{$filename}");
            if (File::exists($videoPath)) {
                File::delete($videoPath);
            }
            
            // Delete metadata
            $metadataPath = storage_path("app/public/videos/metadata/{$filename}.json");
            if (File::exists($metadataPath)) {
                File::delete($metadataPath);
            }
            
            // Delete thumbnail if exists
            $thumbnailPath = public_path("storage/videos/thumbnails/{$filename}.jpg");
            if (File::exists($thumbnailPath)) {
                File::delete($thumbnailPath);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Vídeo excluído com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir vídeo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function assignToHighlight(Request $request)
    {
        $request->validate([
            'video_url' => 'required|url',
            'highlight_number' => 'required|integer|min:1|max:10'
        ]);
        
        try {
            $videoUrl = $request->video_url;
            $highlightNumber = $request->highlight_number;
            
            // Extract filename from URL if it's a local video
            $filename = null;
            if (Str::contains($videoUrl, 'storage/videos/')) {
                $filename = basename($videoUrl);
            }
            
            // If it's an external URL, download it
            if (!$filename && !Str::contains($videoUrl, asset(''))) {
                $videoContent = file_get_contents($videoUrl);
                $filename = "highlight{$highlightNumber}_" . time() . ".mp4";
                
                $path = public_path("videos");
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }
                
                File::put(public_path("videos/highlight{$highlightNumber}.mp4"), $videoContent);
            } else if ($filename) {
                // Copy from storage to highlights
                $sourcePath = public_path("storage/videos/{$filename}");
                $destPath = public_path("videos/highlight{$highlightNumber}.mp4");
                
                if (File::exists($sourcePath)) {
                    File::copy($sourcePath, $destPath);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Arquivo de vídeo não encontrado na biblioteca'
                    ], 404);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Vídeo atribuído ao Destaque {$highlightNumber} com sucesso!",
                'url' => asset("videos/highlight{$highlightNumber}.mp4")
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atribuir vídeo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function getVideosFromStorage()
    {
        $videos = [];
        $videoDir = storage_path('app/public/videos');
        $metadataDir = storage_path('app/public/videos/metadata');
        
        \Log::info('Scanning video directory: ' . $videoDir);
        \Log::info('Directory exists: ' . (File::exists($videoDir) ? 'yes' : 'no'));
        
        if (File::exists($videoDir)) {
            $files = File::allFiles($videoDir);
            \Log::info('Found files: ' . count($files));
            
            foreach ($files as $file) {
                \Log::info('Processing file: ' . $file->getFilename() . ' extension: ' . $file->getExtension());
                
                if ($file->getExtension() === 'mp4' || $file->getExtension() === 'mov' || 
                    $file->getExtension() === 'avi' || $file->getExtension() === 'webm') {
                    
                    $filename = $file->getFilename();
                    $metadataPath = $metadataDir . '/' . $filename . '.json';
                    $metadata = [];
                    
                    if (File::exists($metadataPath)) {
                        $metadata = json_decode(File::get($metadataPath), true);
                    }
                    
                    $videoData = [
                        'id' => $filename,
                        'title' => $metadata['title'] ?? pathinfo($filename, PATHINFO_FILENAME),
                        'description' => $metadata['description'] ?? '',
                        'url' => asset('storage/videos/' . $filename),
                        'thumbnail' => $this->generateThumbnail($filename),
                        'size' => $this->formatFileSize($file->getSize()),
                        'uploaded_at' => $metadata['uploaded_at'] ?? date('Y-m-d H:i:s', $file->getMTime()),
                        'mime_type' => $file->getMimeType()
                    ];
                    
                    \Log::info('Video data: ' . json_encode($videoData));
                    $videos[] = $videoData;
                }
            }
        } else {
            \Log::warning('Video directory does not exist: ' . $videoDir);
        }
        
        // Sort by upload date (newest first)
        usort($videos, function($a, $b) {
            return strtotime($b['uploaded_at']) - strtotime($a['uploaded_at']);
        });
        
        \Log::info('Returning ' . count($videos) . ' videos');
        return $videos;
    }
    
    private function generateThumbnail($filename)
    {
        $thumbnailPath = public_path("storage/videos/thumbnails/{$filename}.jpg");
        $videoPath = storage_path("app/public/videos/{$filename}");
        
        \Log::info('Generating thumbnail for: ' . $filename);
        \Log::info('Video path: ' . $videoPath);
        \Log::info('Video exists: ' . (File::exists($videoPath) ? 'yes' : 'no'));
        
        if (!File::exists($thumbnailPath) && File::exists($videoPath)) {
            // Try to generate thumbnail using FFmpeg (if available)
            try {
                $thumbnailDir = dirname($thumbnailPath);
                if (!File::exists($thumbnailDir)) {
                    File::makeDirectory($thumbnailDir, 0755, true);
                }
                
                // Use FFmpeg to generate thumbnail at 1 second
                $command = "ffmpeg -i " . escapeshellarg($videoPath) . " -ss 00:00:01 -vframes 1 " . escapeshellarg($thumbnailPath) . " 2>&1";
                \Log::info('Running command: ' . $command);
                exec($command, $output, $returnCode);
                
                \Log::info('FFmpeg return code: ' . $returnCode);
                \Log::info('FFmpeg output: ' . implode("\n", $output));
                
                if ($returnCode === 0 && File::exists($thumbnailPath)) {
                    \Log::info('Thumbnail generated successfully');
                    return asset("storage/videos/thumbnails/{$filename}.jpg");
                }
            } catch (\Exception $e) {
                \Log::warning('FFmpeg not available or failed: ' . $e->getMessage());
            }
        }
        
        if (File::exists($thumbnailPath)) {
            return asset("storage/videos/thumbnails/{$filename}.jpg");
        }
        
        // Return default video icon
        return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23667eea"><path d="M8 5v14l11-7z"/></svg>');
    }
    
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
