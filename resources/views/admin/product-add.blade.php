@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Add Product</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><a href="{{ route('admin.products') }}"><div class="text-tiny">Products</div></a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><div class="text-tiny">Add product</div></li>
                </ul>
            </div>

            <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data"
                action="{{ route('admin.product.store') }}">
                @csrf

                <!-- Basic Information -->
                <div class="wg-box">
                    <h4 class="mb-3">Basic Information</h4>

                    <fieldset class="name mb-4">
                        <div class="body-title mb-10">Product Name <span class="tf-color-1">*</span></div>
                        <input class="form-control" type="text" placeholder="Enter product name"
                            name="name" value="{{ old('name') }}" required>
                        @error('name')<span class="alert alert-danger d-block mt-2">{{ $message }}</span>@enderror
                    </fieldset>

                    <fieldset class="name mb-4">
                        <div class="body-title mb-10">Product Slug <span class="tf-color-1">*</span></div>
                        <input class="form-control" type="text" placeholder="Enter product slug"
                            name="slug" value="{{ old('slug') }}" required>
                        @error('slug')<span class="alert alert-danger d-block mt-2">{{ $message }}</span>@enderror
                    </fieldset>

                    <div class="row gap-3 mb-4">
                        <div class="col-md-6">
                            <fieldset>
                                <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                                <select class="form-control" name="category_id" required>
                                    <option value="">-- Choose Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<span class="alert alert-danger d-block mt-2">{{ $message }}</span>@enderror
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset>
                                <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                                <select class="form-control" name="brand_id" required>
                                    <option value="">-- Choose Brand --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id')<span class="alert alert-danger d-block mt-2">{{ $message }}</span>@enderror
                            </fieldset>
                        </div>
                    </div>

                    <div class="row gap-3 mb-4">
                        <div class="col-md-6">
                            <fieldset>
                                <div class="body-title mb-10">Featured Product</div>
                                <select class="form-control" name="featured">
                                    <option value="0" {{ old('featured') == '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('featured') == '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>

                    <fieldset class="mb-4">
                        <div class="body-title mb-10">Short Description <span class="tf-color-1">*</span></div>
                        <textarea class="form-control" rows="3" name="short_description" required>{{ old('short_description') }}</textarea>
                        @error('short_description')<span class="alert alert-danger d-block mt-2">{{ $message }}</span>@enderror
                    </fieldset>

                    <fieldset class="mb-4">
                        <div class="body-title mb-10">Full Description <span class="tf-color-1">*</span></div>
                        <textarea class="form-control" rows="5" name="description" required>{{ old('description') }}</textarea>
                        @error('description')<span class="alert alert-danger d-block mt-2">{{ $message }}</span>@enderror
                    </fieldset>
                </div>

                <!-- Images -->
                <div class="wg-box">
                    <h4 class="mb-3">Product Images</h4>

                    <fieldset class="mb-4">
                        <div class="body-title mb-10">Featured Image <span class="tf-color-1">*</span></div>
                        <div class="upload-image flex-grow">
                            <div class="item" id="imgpreview" style="display:none; position:relative;">
                                <img src="" class="effect8 img-thumbnail" alt="" style="max-width:200px;">
                                <button type="button" class="btn btn-sm btn-danger rounded-circle remove-img-main position-absolute shadow-sm"
                                    style="top:-8px;right:-8px;width:24px;height:24px;display:flex;align-items:center;justify-content:center;padding:0;font-size:14px;">&times;</button>
                            </div>
                            <div id="upload-file" class="item up-load">
                                <label class="uploadfile" for="myFile">
                                    <span class="icon"><i class="icon-upload-cloud"></i></span>
                                    <span class="body-text">Drop your image here or <span class="tf-color">click to browse</span></span>
                                    <input type="file" id="myFile" name="image" accept="image/*">
                                </label>
                            </div>
                        </div>
                        @error('image')<span class="alert alert-danger d-block mt-2">{{ $message }}</span>@enderror
                    </fieldset>

                    <fieldset>
                        <div class="body-title mb-10">Gallery Images</div>
                        <div class="upload-image mb-16">
                            <div id="galUpload" class="item up-load">
                                <label class="uploadfile" for="gFile">
                                    <span class="icon"><i class="icon-upload-cloud"></i></span>
                                    <span class="text-tiny">Drop images here or <span class="tf-color">click to browse</span></span>
                                    <input type="file" id="gFile" name="images[]" accept="image/*" multiple>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Variants -->
                <div class="wg-box">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="mb-0">Product Variants <span class="tf-color-1">*</span></h4>
                        <button type="button" class="tf-button btn-sm" id="addVariantBtn">+ Add Variant</button>
                    </div>
                    <p class="text-muted mb-3">Each variant has its own price, stock, and weight (e.g. "1 KG Box", "5 KG Crate").</p>

                    @error('variants')<div class="alert alert-danger mb-3">{{ $message }}</div>@enderror

                    <div id="variants-container">
                        {{-- JS will render rows here; seed one by default --}}
                    </div>
                </div>

                <!-- Submit -->
                <div class="wg-box">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="tf-button w-full" type="submit">Add Product</button>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.products') }}" class="tf-button w-full" style="background:#6c757d;border-color:#6c757d;text-decoration:none;">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Variant row template (hidden) --}}
    <template id="variant-row-tpl">
        <div class="variant-row border rounded p-3 mb-3 bg-light position-relative">
            <button type="button" class="btn btn-sm btn-danger rounded-circle remove-variant-btn position-absolute"
                style="top:-10px;right:-10px;width:26px;height:26px;padding:0;font-size:14px;">&times;</button>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-500">Variant Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="variants[__IDX__][variant_name]" placeholder="e.g. 1 KG Box" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-500">Weight</label>
                    <input type="number" class="form-control" name="variants[__IDX__][weight]" placeholder="e.g. 1.5" step="0.01" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-500">Unit <span class="text-danger">*</span></label>
                    <select class="form-control" name="variants[__IDX__][unit]" required>
                        <option value="KG">KG</option>
                        <option value="G">G</option>
                        <option value="LB">LB</option>
                        <option value="PCS">PCS</option>
                        <option value="BOX">BOX</option>
                        <option value="L">L</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-500">Price (Rs) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="variants[__IDX__][price]" placeholder="0.00" step="0.01" min="0" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-500">Compare Price</label>
                    <input type="number" class="form-control" name="variants[__IDX__][compare_price]" placeholder="0.00" step="0.01" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-500">Cost Price</label>
                    <input type="number" class="form-control" name="variants[__IDX__][cost_price]" placeholder="0.00" step="0.01" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-500">Stock Qty <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="variants[__IDX__][stock_qty]" placeholder="0" min="0" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-500">Low Stock Alert</label>
                    <input type="number" class="form-control" name="variants[__IDX__][low_stock_alert]" placeholder="5" min="0" value="5">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-500">SKU</label>
                    <input type="text" class="form-control" name="variants[__IDX__][sku]" placeholder="Auto-generated if blank">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-500">Barcode</label>
                    <input type="text" class="form-control" name="variants[__IDX__][barcode]" placeholder="Optional barcode">
                </div>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
<script>
$(function(){
    let variantIdx = 0;

    function addVariantRow(defaults) {
        const tpl = document.getElementById('variant-row-tpl').innerHTML;
        const html = tpl.replace(/__IDX__/g, variantIdx);
        const $row = $(html);
        if (defaults) {
            Object.entries(defaults).forEach(([k, v]) => {
                $row.find(`[name="variants[${variantIdx}][${k}]"]`).val(v);
            });
        }
        $('#variants-container').append($row);
        variantIdx++;
    }

    // Add one row on load
    addVariantRow();

    $('#addVariantBtn').on('click', function() { addVariantRow(); });

    $(document).on('click', '.remove-variant-btn', function() {
        if ($('.variant-row').length > 1) {
            $(this).closest('.variant-row').remove();
        } else {
            alert('At least one variant is required.');
        }
    });

    // Slug from name
    $('input[name="name"]').on('keyup change', function() {
        const slug = $(this).val().trim().toLowerCase()
            .replace(/[^\w\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^-+|-+$/g, '');
        $('input[name="slug"]').val(slug);
    });

    // Main image preview
    $('#myFile').on('change', function() {
        const [file] = this.files;
        if (file) { $('#imgpreview img').attr('src', URL.createObjectURL(file)); $('#imgpreview').show(); }
    });
    $(document).on('click', '.remove-img-main', function() {
        $('#imgpreview').hide(); $('#imgpreview img').attr('src', ''); $('#myFile').val('');
    });

    // Gallery images
    const gdt = new DataTransfer();
    $('#gFile').on('change', function() {
        $.each(this.files, function(i, file) {
            gdt.items.add(file);
            const reader = new FileReader();
            reader.onload = e => {
                const img = $('<img>').attr('src', e.target.result).addClass('effect8 img-thumbnail').css('max-width', '100px');
                const btn = $('<button>').attr('type','button').addClass('btn btn-sm btn-danger rounded-circle remove-img position-absolute shadow-sm')
                    .css({top:'-8px',right:'-8px',width:'24px',height:'24px',display:'flex',alignItems:'center',justifyContent:'center',padding:'0',fontSize:'14px'})
                    .html('&times;').data('filename', file.name);
                $('#galUpload').before($('<div>').addClass('item gitems').css('position','relative').append(img).append(btn));
            };
            reader.readAsDataURL(file);
        });
        this.files = gdt.files;
    });
    $(document).on('click', '.remove-img', function() {
        const fn = $(this).data('filename');
        for (let i = 0; i < gdt.items.length; i++) {
            if (gdt.items[i].getAsFile().name === fn) { gdt.items.remove(i); break; }
        }
        $(this).closest('.gitems').remove();
    });
});
</script>
@endpush
