<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Blog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;


#[Layout('components.layouts.admin')]
#[Title('Dashboard')]


class BlogsManagement extends Component
{
    public $title, $description, $image;
    public $blog_id; // ✅ for editing

    public function saveBlog()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
            // Accept any non-empty image string (URL or path). Strict URL rule removed
            'image' => 'required',
        ]);

        Blog::create([
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
        ]);

        $this->reset();

        session()->flash('success', 'Blog Added Successfully!');
        $this->dispatch('close-add-modal');
        $this->dispatch('alert'); // 👈 auto dismiss alert JS trigger
    }


    // ✅ Fill fields for edit modal
public function editBlog($id)
{
    $blog = Blog::findOrFail($id);
    $this->blog_id = $blog->id;
    $this->title = $blog->title;
    $this->description = $blog->description;
    $this->image = $blog->image;

    $this->dispatch('show-edit-modal'); // ✅ triggers modal open after Livewire updates
}

    // ✅ Update blog

    public function updateBlog()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
            // Accept any non-empty image string (URL or path). Strict URL rule removed
            'image' => 'required',
        ]);

        Blog::where('id', $this->blog_id)->update([
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
        ]);

        $this->reset();

        session()->flash('success', 'Blog Updated Successfully!') ;
        $this->dispatch('close-edit-modal');
        $this->dispatch('alert');
    }
    // ✅ Delete blog
    public function deleteBlog($id)
    {
        Blog::findOrFail($id)->delete();
        session()->flash('success', 'Blog Deleted Successfully!');
    }
    

    public function render()
    {
        return view('livewire.admin.blogs-management', [
            'blogs' => Blog::latest()->get()
        ]);
    }
}
