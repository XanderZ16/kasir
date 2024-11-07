<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{

    public function index()
    {
        $dataBarang = Barang::all();
        return view('DataBarang', compact('dataBarang'));
    }

    public function searchByBarcode(Request $request)
    {
        $barcode = $request->query('barcode');
        $dataBarang = Barang::where('barcode', $barcode)->get();

        return view('DataBarang', compact('dataBarang'));
    }



    public function CreateBarangForm(){
        return view('CreateBarang');
    }

   public function CreateBarang(Request $request){
    // Convert formatted harga to an integer
    $request->merge([
        'harga_beli' => str_replace('.', '', $request->harga_beli),
        'harga_jual' => str_replace('.', '', $request->harga_jual),
    ]);

    // Validate the request
    $request->validate([
        'barcode' => 'required|string|unique:barang',
        'nama_barang' => 'required|string|unique:barang',
        'harga_beli' => 'required|integer|min:0',
        'harga_jual' => 'required|integer|min:0',
        'stok' => 'required|integer|min:0',
        'image' => 'required|image',
        'status' => 'required|string',
    ]);

    // Store the image
    $path = $request->file('image')->store('barang', 'public');

    // Create the new Barang record
    Barang::create([
        'barcode' => $request->barcode,
        'nama_barang' => $request->nama_barang,
        'harga_beli' => $request->harga_beli, // This is now an integer
        'harga_jual' => $request->harga_jual, // This is now an integer
        'stok' => $request->stok,
        'gambar' => $path,
        'status' => $request->status,
    ]);

    // Redirect with success message
    return redirect()->route('barang.index')->with('success', 'Berhasil menambahkan data');
}


    public function DeleteBarang($id){
        $barang = Barang::where('id' , $id)->firstorfail();
        $barang->delete();

        return back()->with('success' , 'berhasil menghapus data');
    }

    public function EditBarangForm($id){
        $barang = Barang::where('id' , $id)->firstorfail();
        return view('EditBarang' , compact('barang'));
    }

    public function EditBarang(Request $request, $id)
    {
        $barang = Barang::find($id);

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|string|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $barang->nama_barang = strtoupper($request->input('nama_barang'));
        $barang->harga_beli = $request->input('harga_beli');
        $barang->harga_jual = $request->input('harga_jual');
        $barang->stok = $request->input('stok');
        $barang->status = $request->input('status');

        if ($request->hasFile('image')) {
            // Delete the old image if exists
            if ($barang->gambar) {
                Storage::delete('public/' . $barang->gambar);
            }

            // Store the new image
            $fileName = time().'.'.$request->image->extension();
            $path = $request->image->storeAs('barang', $fileName, 'public');
            $barang->gambar = $path;
        }

        $barang->save();

        return redirect('/data-barang')->with('success', 'Product updated successfully.');
    }

    public function manageStock($id)
    {
        $barang = Barang::findOrFail($id);
        return view('KelolaStok', compact('barang'));
    }

    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock_change' => 'required|integer',
            'stock_change_type' => 'required|in:add,subtract',
        ]);

        $barang = Barang::findOrFail($id);

        if ($request->stock_change_type === 'add') {
            $barang->stok += $request->stock_change;
        } elseif ($request->stock_change_type === 'subtract') {
            if ($barang->stok < $request->stock_change) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi untuk pengurangan ini.');
            }
            $barang->stok -= $request->stock_change;
        }

        $barang->save();

        return redirect()->route('barang.index')->with('success', 'Stok berhasil diperbarui.');
    }


}
