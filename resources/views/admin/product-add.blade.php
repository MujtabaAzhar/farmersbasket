@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Add Product</h3>
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
                        <div class="text-tiny">Add product</div>
                    </li>
                </ul>
            </div>

            <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data"
                action="{{ route('admin.product.store') }}">
                @csrf

                <!-- Basic Product Information -->
                <div class="wg-box">
                    <h4 class="mb-3">Basic Information</h4>
                    
                    <fieldset class="name mb-4">
                        <div class="body-title mb-10">Product Name <span class="tf-color-1">*</span></div>
                        <input class="form-control" type="text" placeholder="Enter product name"
                            name="name" value="{{ old('name') }}" aria-required="true" required="">
                        <small class="text-muted">Maximum 100 characters</small>
                        @error('name')
                            <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name mb-4">
                        <div class="body-title mb-10">Product Slug <span class="tf-color-1">*</span></div>
                        <input class="form-control" type="text" placeholder="Enter product slug"
                            name="slug" value="{{ old('slug') }}" aria-required="true" required="">
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
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
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
                            required="">{{ old('short_description') }}</textarea>
                        <small class="text-muted">Brief overview of the product</small>
                        @error('short_description')
                            <span class="alert alert-danger d-block mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="mb-4">
                        <div class="body-title mb-10">Full Description <span class="tf-color-1">*</span></div>
                        <textarea class="form-control" rows="5" name="description"
                            placeholder="Detailed product description" aria-required="true"
                            required="">{{ old('description') }}</textarea>
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
                            <div class="item" id="imgpreview" style="display:none; position: relative;">
                                <img src="" class="effect8 img-thumbnail" alt="" style="max-width: 200px;">
                                <button type="button" class="btn btn-sm btn-danger rounded-circle remove-img-main position-absolute shadow-sm" style="top: -8px; right: -8px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; padding: 0; font-size: 14px; line-height: 1;">&times;</button>
                            </div>
                            <div id="upload-file" class="item up-load">
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
                                    name="regular_price" value="{{ old('regular_price') }}" aria-required="true"
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
                                    name="sale_price" value="{{ old('sale_price') }}" aria-required="true"
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
                                    <option value="instock" {{ old('stock_status') == 'instock' ? 'selected' : '' }}>In Stock</option>
                                    <option value="outofstock" {{ old('stock_status') == 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
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
                                    <option value="0" {{ old('featured') == '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('featured') == '1' ? 'selected' : '' }}>Yes</option>
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
                    <h4 class="mb-3">Product Sizes/Variants <span class="tf-color-1">*</span></h4>
                    <p class="text-muted mb-3">Add up to 5 size variants (e.g., 5 KG, 7 KG, 9 KG, 12 KG, 16 KG) for your product</p>

                    <fieldset class="mb-4">
                        <div class="body-title mb-10">Measurement Unit <span class="tf-color-1">*</span></div>
                        <select class="form-control" name="unit" id="unit" required="">
                            <option value="">-- Select Unit --</option>
                            <option value="KG" {{ old('unit') == 'KG' ? 'selected' : '' }}>KG (Kilogram)</option>
                            <option value="LB" {{ old('unit') == 'LB' ? 'selected' : '' }}>LB (Pound)</option>
                            <option value="G" {{ old('unit') == 'G' ? 'selected' : '' }}>G (Gram)</option>
                            <option value="ML" {{ old('unit') == 'ML' ? 'selected' : '' }}>ML (Milliliter)</option>
                            <option value="L" {{ old('unit') == 'L' ? 'selected' : '' }}>L (Liter)</option>
                            <option value="PCS" {{ old('unit') == 'PCS' ? 'selected' : '' }}>PCS (Pieces)</option>
                        </select>
                    </fieldset>

                    <div id="sizes-container">
                        @for($i = 0; $i < 5; $i++)
                            <div class="size-row mb-3 p-3 bg-light rounded border">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-500">Size {{ $i + 1 }} Value</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control size-value" name="size_values[]" 
                                                placeholder="e.g., 5, 7, 9" step="0.5" value="{{ old('size_values.' . $i) }}">
                                            <span class="input-group-text" id="unit-display">KG</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-500">Quantity</label>
                                        <input type="number" class="form-control size-quantity" name="size_quantities[]" 
                                            placeholder="Enter quantity" min="0" value="{{ old('size_quantities.' . $i) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-500">Regular Price</label>
                                        <input type="number" class="form-control size-regular-price" name="size_regular_prices[]" 
                                            placeholder="Enter regular price" step="0.01" value="{{ old('size_regular_prices.' . $i) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-500">Sale Price</label>
                                        <input type="number" class="form-control size-sale-price" name="size_sale_prices[]" 
                                            placeholder="Enter sale price (optional)" step="0.01" value="{{ old('size_sale_prices.' . $i) }}">
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                    <small class="text-muted d-block mt-2"><i class="fas fa-info-circle"></i> Leave size fields blank for unused slots. At least one size must be filled.</small>
                </div>

                <!-- SKU Information -->
                <div class="wg-box" style="background-color: #f8f9fa; border-left: 4px solid #4CAF50;">
                    <h4 class="mb-3"><i class="fas fa-key" style="color: #4CAF50;"></i> Automatic SKU Generation</h4>
                    <div class="alert alert-success mb-0">
                        <p class="mb-0"><strong>✓ SKU will be automatically generated</strong> when you save this product. Format: <code>PROD-{timestamp}-{randomcode}</code></p>
                        <p class="mb-0 mt-2 small">Example: PROD-1715775600-A8K2</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="wg-box">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="tf-button w-full" type="submit" style="background-color: #4CAF50; border-color: #4CAF50;">
                                <i class="fas fa-save"></i> Add Product
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
                    // Convert to lowercase, replace spaces with hyphens, remove special chars
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
                }
            }); 

            // Remove main image
            $(document).on('click', '.remove-img-main', function() {
                $("#imgpreview").hide();
                $("#imgpreview img").attr('src', '');
                $("#myFile").val('');
            });

            // Gallery images
            const gdt = new DataTransfer();

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
                        $('#galUpload').before(div);
                    }
                    reader.readAsDataURL(file);
                });
                this.files = gdt.files;
            }); 

            // Remove gallery image
            $(document).on('click', '.remove-img', function() {
                const filename = $(this).data('filename');
                for (let i = 0; i < gdt.items.length; i++) {
                    if (gdt.items[i].getAsFile().name === filename) {
                        gdt.items.remove(i);
                        break;
                    }
                }
                $(this).closest('.gitems').remove();
            });
        });
    </script>
@endpush
