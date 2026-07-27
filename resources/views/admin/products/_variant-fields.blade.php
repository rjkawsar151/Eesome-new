@foreach([['name','Variant name','text'],['sku','Variant SKU','text'],['color','Color','text'],['size','Size','text'],['material','Material','text'],['price_adjustment','Price adjustment','number'],['stock','Stock','number'],['sort_order','Order','number']] as [$key,$label,$type])
<div class="field"><label>{{ $label }}</label><input class="input" type="{{ $type }}" name="{{ $key }}" value="{{ old($key,$variant->$key ?? ($key==='price_adjustment'?0:null)) }}" @if(in_array($key,['name','sku','price_adjustment','stock','sort_order'])) required @endif></div>
@endforeach
<div class="field"><label>Variant image</label><input class="input" type="file" name="variant_image" accept="image/png,image/webp,image/jpeg"></div>
<label><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$variant->exists?$variant->is_active:true))> Enabled</label>
