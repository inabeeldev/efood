<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Banner;
use App\Model\Category;
use App\Model\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    function index()
    {
        $products = Product::orderBy('name')->where('branch_id',auth('branch')->id())->get();
        $categories = Category::where(['parent_id'=>0])->where('branch_id',auth('branch')->id())->orderBy('name')->get();
        return view('branch-views.banner.index', compact('products', 'categories'));
    }

    function list(Request $request)
    {
        $search = $request->search;
        $query_param = ['search' => $search];

        $banners=Banner::when($search, function ($query) use($search, $query_param){
            $keywords = explode(' ' ,$search);
            foreach ($keywords as $keyword) {
                $query->orWhere('title', 'LIKE', "%$keyword%")
                    ->orwhere('id', 'LIKE', "%$keyword%");
            }
        })
            ->where('branch_id',auth('branch')->id())
            ->latest()
            ->paginate(Helpers::getPagination())
            ->appends($query_param);

        return view('branch-views.banner.list',compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'required',
            'item_type' => 'required'
        ], [
            'title.max' => translate('Title is too long'),
        ]);

        $banner = new Banner;
        $banner->title = $request->title;
        if ($request['item_type'] == 'product') {
            $banner->product_id = $request->product_id;
        } elseif ($request['item_type'] == 'category') {
            $banner->category_id = $request->category_id;
        }
        $banner->image = Helpers::upload('banner/', 'png', $request->file('image'));
        $banner->branch_id = auth('branch')->id();
        $banner->save();
        Toastr::success(translate('Banner added successfully!'));
        return redirect('branch/banner/list');
    }

    public function edit($id)
    {
        $products = Product::orderBy('name')->get();
        $banner = Banner::find($id);
        $categories = Category::where(['parent_id'=>0])->orderBy('name')->get();
        return view('branch-views.banner.edit', compact('banner', 'products', 'categories'));
    }

    public function status(Request $request)
    {
        $banner = Banner::find($request->id);
        $banner->status = $request->status;
        $banner->save();
        Toastr::success(translate('Banner status updated!'));
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'item_type' => 'required'
        ], [
            'title.max' => translate('Title is too long!'),
        ]);

        $banner = Banner::find($id);
        $banner->title = $request->title;
        if ($request['item_type'] == 'product') {
            $banner->product_id = $request->product_id;
            $banner->category_id = null;
        } elseif ($request['item_type'] == 'category') {
            $banner->product_id = null;
            $banner->category_id = $request->category_id;
        }
        $banner->image = $request->has('image') ? Helpers::update('banner/', $banner->image,'png', $request->file('image')):$banner->image;
        $banner->save();
        Toastr::success(translate('Banner updated successfully!'));
        return redirect('branch/banner/list');
    }

    public function delete(Request $request)
    {
        $banner = Banner::find($request->id);
        Helpers::delete('banner/' . $banner['image']);
        $banner->delete();
        Toastr::success(translate('Banner removed!'));
        return back();
    }
}
