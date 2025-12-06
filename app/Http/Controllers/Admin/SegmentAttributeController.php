<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SegmentAttributeController extends Controller
{
    public function index($segmentId)
    {
        return view('admin.attributes.segment.index', compact('segmentId'));
    }

    public function assign(Request $request, $segmentId)
    {
        // attach attribute to segment
        return back()->with('success','Atributo atribuído ao segmento');
    }

    public function unassign(Request $request, $segmentId)
    {
        // detach
        return back()->with('success','Atributo removido do segmento');
    }
}
