<?php
namespace App\Http\Controllers;
use App\Models\NfcData;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NfcController extends Controller
{
    // 接口2: NFC 数据写入
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nfc_id' => 'required|string',
                'wechat_id' => 'required|string',
                'data_type' => 'required|integer|in:1,2,3,4',
                'data_content' => 'required|string',
                'title' => 'required|string',
                'remarks' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $data = NfcData::create([
            'nfc_id' => $validated['nfc_id'],
            'wechat_id' => $validated['wechat_id'],
            'data_type' => $validated['data_type'],
            'data_content' => $validated['data_content'],
            'title' => $validated['title'],
            'remarks' => $validated['remarks'] ?? null,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 201);
    }

    // 接口3: 通过 NFC ID 查询最新数据
    public function showByNfcId($nfcId)
    {
        $data = NfcData::where('nfc_id', $nfcId)
            ->latest('created_at')
            ->first();

        return response()->json([
            'success' => $data ? true : false,
            'data'    => $data ?? null,
        ]);
    }

    // 接口4: 通过微信号查询数据列表
    public function showByWechatId(Request $request, $wechatId)
    {
        $perPage = $request->input('per_page', 15); // 默认每页15条
        $page = $request->input('page', 1); // 默认第1页
        $data_type = $request->input('data_type', ''); //

        $query = NfcData::where('wechat_id', $wechatId)
            ->orderBy('created_at', 'desc');

//        echo $data_type;die;
        // 可选: 按数据类型筛选
        if (!empty($data_type)) {
            $data_type = trim($data_type, ',');
            $data_types = explode(',', $data_type);
            $query->whereIn('data_type', $data_types);
        }

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => $data->isNotEmpty(),
            'data'    => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ]
        ]);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $query = NfcData::query()->orderBy('created_at', 'desc');

        // 按NFC ID筛选
        if ($request->has('nfc_id')) {
            $query->where('nfc_id', $request->nfc_id);
        }

        // 按微信号筛选
        if ($request->has('wechat_id')) {
            $query->where('wechat_id', $request->wechat_id);
        }

        // 按数据类型筛选
        if ($request->has('data_type')) {
            $query->where('data_type', $request->data_type);
        }

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => $data->isNotEmpty(),
            'data'    => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ]
        ]);
    }
}
