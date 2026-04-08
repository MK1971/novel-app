<?php

namespace App\Http\Controllers;

use App\Models\InlineEdit;
use Illuminate\Http\Request;

class InlineEditController extends Controller
{
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Paragraph suggestions use the $2 paid checkout. Open “Suggest Paragraph Edit”, then continue to PayPal — free submissions are not accepted.',
        ], 422);
    }

    public function destroy($id)
    {
        $inlineEdit = InlineEdit::findOrFail($id);

        if (auth()->id() !== $inlineEdit->user_id && ! auth()->user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $inlineEdit->delete();

        return response()->json(['success' => true, 'message' => 'Inline edit deleted successfully!']);
    }
}
