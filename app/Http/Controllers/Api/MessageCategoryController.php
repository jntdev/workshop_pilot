<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MessageCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = MessageCategory::ordered()->get();

        return response()->json($categories->map(fn ($cat) => $this->formatCategory($cat)));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
        ]);

        $slug = Str::slug($validated['label']);

        // Ensure unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (MessageCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        // Get max position
        $maxPosition = MessageCategory::where('is_default', false)->max('position') ?? 0;

        $category = MessageCategory::create([
            'slug' => $slug,
            'label' => $validated['label'],
            'color' => $validated['color'] ?? '#6b7280',
            'position' => $maxPosition + 1,
            'is_default' => false,
        ]);

        return response()->json($this->formatCategory($category), 201);
    }

    public function update(Request $request, MessageCategory $messageCategory): JsonResponse
    {
        $validated = $request->validate([
            'label' => 'sometimes|required|string|max:100',
            'color' => 'sometimes|nullable|string|max:7',
            'position' => 'sometimes|integer|min:0',
        ]);

        $messageCategory->update($validated);

        return response()->json($this->formatCategory($messageCategory));
    }

    public function destroy(MessageCategory $messageCategory): JsonResponse
    {
        if ($messageCategory->is_default) {
            return response()->json(['message' => 'Cannot delete the default category'], 422);
        }

        // Move messages to default category
        $defaultCategory = MessageCategory::getDefault();
        if ($defaultCategory) {
            $messageCategory->messages()->update(['category_id' => $defaultCategory->id]);
        }

        $messageCategory->delete();

        return response()->json(null, 204);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:message_categories,id',
        ]);

        foreach ($validated['order'] as $position => $id) {
            MessageCategory::where('id', $id)->update(['position' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }

    protected function formatCategory(MessageCategory $category): array
    {
        return [
            'id' => $category->id,
            'slug' => $category->slug,
            'label' => $category->label,
            'color' => $category->color,
            'position' => $category->position,
            'is_default' => $category->is_default,
        ];
    }
}
