@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Edit Product</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('admin.products') }}">
                            <div class="text-tiny">Products</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit: {{ $product->name }}</div>
                    </li>
                </ul>
            </div>

            <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data"
                action="{{ route('admin.product.update', $product->id) }}">
                @csrf
                @method('PUT')

                <!-- Basic Product Information -->
                <div class="wg-box">
                    <h4 class="mb-3">Basic Information</h4>
                    
                    <fieldset class="name mb-4">
                        <div class="body-title mb-10">Product Name <span class="tf-color-1">*</span></div>
                        <input class="form-control" type="text" placeholder="Enter product name"
                            name="name" value="{{ $product->name }}" aria-required="true" required="">
                        <small class="text-muted">Maximum 100 characters</small>
                        @error('name')
                            <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name mb-4">
                        <div class="body-title mb-10">Product Slug <span class="tf-color-1">*</span></div>
                        <input class="form-control" type="text" placeholder="Enter product slug"
                            name="slug" value="{{ $product->slug }}" aria-required="true" required="">
                        <small class="text-muted">URL-friendly name (auto-generated from product name)</small>
                        @error('slug')
                            <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <div class="row gap-3 mb-4">
                        <div class="col-md-6">
                            <fieldset>
                                <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                                <select class="form-control" name="category_id" required="">
                                    <option value="">-- Choose Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset>
                                <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                                <select class="form-control" name="brand_id" required="">
                                    <option value="">-- Choose Brand --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                                @enderror
                            </fieldset>
                        </div>
                    </div>

                    <fieldset class="mb-4">
                        <div class="body-title mb-10">Short Description <span class="tf-color-1">*</span></div>
                        <textarea class="form-control" rows="3" name="short_description"
                            placeholder="Short product description" aria-required="true"
                            required="">{{ $product->short_description }}</textarea>
                        <small class="text-muted">Brief overview of the product</small>
                        @error('short_description')
                            <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="mb-4">
                        <div class="body-title mb-10">Full Description <span class="tf-color-1">*</span></div>
                        <textarea class="form-control" rows="5" name="description"
                            placeholder="Detailed product description" aria-required="true"
                            required="">{{ $product->description }}</textarea>
                        <small class="text-muted">Complete details about the product</small>
                        @error('description')
                            <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>
                </div>

                <!-- Images -->
                <div class="wg-box">
                    <h4 class="mb-3">Product Images</h4>

                    <fieldset class="mb-4">
                        <div class="body-title mb-10">Featured Image <span class="tf-color-1">*</span></div>
                        <div class="upload-image flex-grow">
                            @if($product->image)
                                <div class="item" id="imgpreview" style="position: relative;">
                                    <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}" class="effect8 img-thumbnail" alt="{{ $product->name }}" style="max-width: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger rounded-circle remove-img-main position-absolute shadow-sm" style="top: -8px; right: -8px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; padding: 0; font-size: 14px; line-height: 1;">&times;</button>
                                </div>
                            @else
                                <div class="item" id="imgpreview" style="display:none; position: relative;">
                                    <img src="" class="effect8 img-thumbnail" alt="" style="max-width: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger rounded-circle remove-img-main position-absolute shadow-sm" style="top: -8px; right: -8px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; padding: 0; font-size: 14px; line-height: 1;">&times;</button>
                                </div>
                            @endif
                            <div id="upload-file" class="item up-load" @if($product->image) style="display:none;" @endif>
                                <label class="uploadfile" for="myFile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="body-text">Drop your image here or <span class="tf-color">click to browse</span></span>
                                    <input type="file" id="myFile" name="image" accept="image/*">
                                </label>
                            </div>
                        </div>
                        @error('image')
                            <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset>
                        <div class="body-title mb-10">Gallery Images</div>
                        <div class="upload-image mb-16">
                            @if($product->images)
                                @foreach(explode(',', $product->images) as $img)
                                    @if(trim($img))
                                        <div class="item gitems" style="position: relative;">
                                            <img src="{{ asset('uploads/products/thumbnails/' . trim($img)) }}" class="effect8 img-thumbnail" alt="" style="max-width: 100px;">
                                            <button type="button" class="btn btn-sm btn-danger rounded-circle remove-img position-absolute shadow-sm" style="top: -8px; right: -8px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; padding: 0; font-size: 14px; line-height: 1;" data-filename="{{ trim($img) }}">&times;</button>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <!-- Hidden field to track current gallery images -->
                            <input type="hidden" id="current-images-field" name="current_images" value="">
                            <div id="galUpload" class="item up-load">
                                <label class="uploadfile" for="gFile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="text-tiny">Drop images here or <span class="tf-color">click to browse</span></span>
                                    <input type="file" id="gFile" name="images[]" accept="image/*" multiple="">
                                </label>
                            </div>
                        </div>
                        @error('images')
                            <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>
                </div>

                <!-- Pricing -->
                <div class="wg-box">
                    <h4 class="mb-3">Pricing</h4>

                    <div class="row gap-3 mb-4">
                        <div class="col-md-6">
                            <fieldset>
                                <div class="body-title mb-10">Regular Price <span class="tf-color-1">*</span></div>
                                <input class="form-control" type="number" placeholder="Enter regular price" step="0.01"
                                    name="regular_price" value="{{ $product->regular_price }}" aria-required="true"
                                    required="">
                                @error('regular_price')
                                    <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset>
                                <div class="body-title mb-10">Sale Price <span class="tf-color-1">*</span></div>
                                <input class="form-control" type="number" placeholder="Enter sale price" step="0.01"
                                    name="sale_price" value="{{ $product->sale_price }}" aria-required="true"
                                    required="">
                                @error('sale_price')
                                    <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                                @enderror
                            </fieldset>
                        </div>
                    </div>

                    <div class="row gap-3 mb-4">
                        <div class="col-md-6">
                            <fieldset>
                                <div class="body-title mb-10">Stock Status <span class="tf-color-1">*</span></div>
                                <select class="form-control" name="stock_status" required="">
                                    <option value="">-- Choose Status --</option>
                                    <option value="instock" {{ $product->stock_status == 'instock' ? 'selected' : '' }}>In Stock</option>
                                    <option value="outofstock" {{ $product->stock_status == 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                                @error('stock_status')
                                    <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset>
                                <div class="body-title mb-10">Featured Product</div>
                                <select class="form-control" name="featured">
                                    <option value="0" {{ $product->featured == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $product->featured == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('featured')
                                    <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                                @enderror
                            </fieldset>
                        </div>
                    </div>
                </div>

                <!-- Product Variants/Sizes -->
                <div class="wg-box">
                    <h4 class="mb-3">Product Sizes/Variants</h4>
                    <p class="text-muted mb-3">Edit up to 5 size variants. You can also manage quantities from the <a href="{{ route('admin.product.quantity') }}" class="text-primary">Quantity Management</a> page.</p>

                    <fieldset class="mb-4">
                        <div class="body-title mb-10">Unit</div>
                        <select class="form-control" name="unit" id="unit">
                            <option value="KG" {{ $product->sizes()->first()?->unit == 'KG' ? 'selected' : 'selected' }}>KG (Kilogram)</option>
                            <option value="LB" {{ $product->sizes()->first()?->unit == 'LB' ? 'selected' : '' }}>LB (Pound)</option>
                            <option value="G" {{ $product->sizes()->first()?->unit == 'G' ? 'selected' : '' }}>G (Gram)</option>
                            <option value="ML" {{ $product->sizes()->first()?->unit == 'ML' ? 'selected' : '' }}>ML (Milliliter)</option>
                            <option value="L" {{ $product->sizes()->first()?->unit == 'L' ? 'selected' : '' }}>L (Liter)</option>
                            <option value="PCS" {{ $product->sizes()->first()?->unit == 'PCS' ? 'selected' : '' }}>PCS (Pieces)</option>
                        </select>
                    </fieldset>

                    <div id="sizes-container">
                        @for($i = 0; $i < 5; $i++)
                            @php
                                $size = $product->sizes()->skip($i)->first();
                            @endphp
                            <div class="size-row mb-3 p-3 bg-light rounded border">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-500">Size {{ $i + 1 }} Value</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control size-value" name="size_values[]" 
                                                placeholder="e.g., 5, 7, 9" step="0.5" value="{{ $size?->size_value ?? '' }}">
                                            <span class="input-group-text" id="unit-display">{{ $product->sizes()->first()?->unit ?? 'KG' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-500">Quantity</label>
                                        <input type="number" class="form-control size-quantity" name="size_quantities[]" 
                                            placeholder="Enter quantity" min="0" value="{{ $size?->quantity ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-500">Regular Price</label>
                                        <input type="number" class="form-control size-regular-price" name="size_regular_prices[]" 
                                            placeholder="Enter regular price" step="0.01" value="{{ $size?->regular_price ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-500">Sale Price</label>
                                        <input type="number" class="form-control size-sale-price" name="size_sale_prices[]" 
                                            placeholder="Enter sale price (optional)" step="0.01" value="{{ $size?->sale_price ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                    <small class="text-muted d-block mt-2"><i class="fas fa-info-circle"></i> Leave size fields blank for unused slots.</small>
                </div>

                <!-- SKU Information -->
                <div class="wg-box" style="background-color: #f8f9fa; border-left: 4px solid #4CAF50;">
                    <h4 class="mb-3"><i class="fas fa-key" style="color: #4CAF50;"></i> Product SKU</h4>
                    <div class="alert alert-info mb-0">
                        <p class="mb-0"><strong>Auto-Generated SKU:</strong> <code>{{ $product->SKU }}</code></p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="wg-box">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="tf-button w-full" type="submit" style="background-color: #4CAF50; border-color: #4CAF50;">
                                <i class="fas fa-save"></i> Update Product
                            </button>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.products') }}" class="tf-button w-full" style="background-color: #6c757d; border-color: #6c757d; text-decoration: none;">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        $(function(){
            // Dynamic slug generation from product name
            $('input[name="name"]').on('keyup change', function() {
                const name = $(this).val().trim();
                if(name) {
                    const slug = name.toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
                    
                    $('input[name="slug"]').val(slug);
                }
            });

            // Trigger slug generation on page load if name field has value
            const nameInput = $('input[name="name"]');
            if(nameInput.val().trim()) {
                nameInput.trigger('keyup');
            }

            // Update unit display in size inputs when unit changes
            $('#unit').on('change', function() {
                const unit = $(this).val();
                $('#unit-display').text(unit || 'Unit');
            });

            // Set initial unit display
            $('#unit-display').text($('#unit').val() || 'Unit');

            // Main image preview
            $("#myFile").on("change",function(e){
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr('src',URL.createObjectURL(file));
                    $("#imgpreview").show();
                    $("#upload-file").hide();
                }
            }); 

            // Remove main image
            $(document).on('click', '.remove-img-main', function() {
                $("#imgpreview").hide();
                $("#imgpreview img").attr('src', '');
                $("#myFile").val('');
                $("#upload-file").show();
            });

            // Gallery images - Initialize with existing images
            const gdt = new DataTransfer();
            const existingImages = {{ json_encode(explode(',', $product->images ?? '')) }};
            
            // Track current images for deletion
            function updateCurrentImagesField() {
                const images = [];
                $('.gitems').each(function() {
                    const filename = $(this).find('.remove-img').data('filename');
                    if(filename) images.push(filename);
                });
                $('#current-images-field').val(JSON.stringify(images));
            }
            updateCurrentImagesField();

            $("#gFile").on("change",function(e){                   
               $.each(this.files, function(index, file) {
                    gdt.items.add(file);
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = $('<img>').attr('src', e.target.result).addClass('effect8 img-thumbnail').css('max-width', '100px');
                        const btn = $('<button>').attr('type', 'button').addClass('btn btn-sm btn-danger rounded-circle remove-img position-absolute shadow-sm').css({
                            top: '-8px', right: '-8px', width: '24px', height: '24px', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '0', fontSize: '14px', lineHeight: '1'
                        }).html('&times;').data('filename', file.name);
                        const div = $('<div>').addClass('item gitems').css('position', 'relative').append(img).append(btn);
                        $('#galUpload').before(div);                    updateCurrentImagesField();                    }
                    reader.readAsDataURL(file);
                });
                this.files = gdt.files;
            }); 

            // Remove gallery image
            $(document).on('click', '.remove-img', function(e) {
                e.preventDefault();
                const filename = $(this).data('filename');
                for (let i = 0; i < gdt.items.length; i++) {
                    if (gdt.items[i].getAsFile().name === filename) {
                        gdt.items.remove(i);
                        break;
                    }
                }
                $(this).closest('.gitems').remove();
                updateCurrentImagesField();
            });
        });
    </script>
@endpush
