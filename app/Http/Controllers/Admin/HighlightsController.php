<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class HighlightsController extends Controller
{
    public function index()
    {
        return view('admin.highlights.index');
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'highlights_section_title' => 'nullable|string|max:100',
            'highlights_section_subtitle' => 'nullable|string|max:200',
        ]);
        
        Setting::set('highlights_section_title', $request->highlights_section_title);
        Setting::set('highlights_section_subtitle', $request->highlights_section_subtitle);
        
        return redirect()->route('admin.highlights.index')
            ->with('success', 'Configurações dos destaques atualizadas com sucesso!');
    }
    
    public static function getHighlights()
    {
        $highlights = [];
        
        for ($i = 1; $i <= 10; $i++) {
            $videoPath = public_path("videos/highlight{$i}.mp4");
            $title = Setting::get("highlight{$i}_title", "Destaque {$i}");
            $subtitle = Setting::get("highlight{$i}_subtitle", "Descrição do destaque {$i}");
            
            $highlights[] = [
                'number' => $i,
                'title' => $title,
                'subtitle' => $subtitle,
                'video' => "highlight{$i}.mp4",
                'exists' => file_exists($videoPath),
                'url' => asset("videos/highlight{$i}.mp4")
            ];
        }
        
        return $highlights;
    }
    
    public function uploadVideo(Request $request, $number)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,mov,avi|max:50000', // 50MB max
        ]);
        
        $filename = "highlight{$number}.mp4";
        $path = public_path("videos");
        
        // Create directory if not exists
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        
        // Delete old video if exists
        if (File::exists(public_path("videos/highlight{$number}.mp4"))) {
            File::delete(public_path("videos/highlight{$number}.mp4"));
        }
        
        // Upload new video
        $request->file('video')->move($path, $filename);
        
        return response()->json([
            'success' => true,
            'message' => "Vídeo {$number} atualizado com sucesso!",
            'url' => asset("videos/{$filename}")
        ]);
    }
    
    public function addVideoFromUrl(Request $request, $number)
    {
        $request->validate([
            'url' => 'required|url',
        ]);
        
        $url = $request->url;
        $filename = "highlight{$number}.mp4";
        $path = public_path("videos");
        
        // Create directory if not exists
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        
        try {
            // Download video from URL
            $videoContent = file_get_contents($url);
            
            // Delete old video if exists
            if (File::exists(public_path("videos/highlight{$number}.mp4"))) {
                File::delete(public_path("videos/highlight{$number}.mp4"));
            }
            
            // Save new video
            File::put(public_path("videos/{$filename}"), $videoContent);
            
            return response()->json([
                'success' => true,
                'message' => "Vídeo {$number} baixado e adicionado com sucesso!",
                'url' => asset("videos/{$filename}")
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao baixar o vídeo: ' . $e->getMessage()
            ], 400);
        }
    }
    
    public function deleteVideo($number)
    {
        $videoPath = public_path("videos/highlight{$number}.mp4");
        
        if (File::exists($videoPath)) {
            File::delete($videoPath);
            return response()->json([
                'success' => true,
                'message' => "Vídeo {$number} excluído com sucesso!"
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => "Vídeo {$number} não encontrado!"
        ], 404);
    }
}
