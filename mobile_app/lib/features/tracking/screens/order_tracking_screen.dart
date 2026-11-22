import 'package:flutter/material.dart';
import '../../../core/constants/api_endpoints.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/network/api_client.dart';
import '../../../core/utils/formatters.dart';
import '../../order/models/order_model.dart';
import '../widgets/timeline_stepper.dart';

class OrderTrackingScreen extends StatefulWidget {
  final dynamic orderId;

  const OrderTrackingScreen({super.key, required this.orderId});

  @override
  State<OrderTrackingScreen> createState() => _OrderTrackingScreenState();
}

class _OrderTrackingScreenState extends State<OrderTrackingScreen> {
  final _api = ApiClient();
  OrderModel? _orderData;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchTrackingData();
  }

  Future<void> _fetchTrackingData() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.get(ApiEndpoints.orderTrack(widget.orderId));
      if (res.data['success'] == true) {
        _orderData = OrderModel.fromJson(res.data['data']);
      }
    } catch (_) {}
    if (mounted) setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Status Monitoring Laundry'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _fetchTrackingData,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _orderData == null
              ? const Center(child: Text('Gagal memuat data tracking.'))
              : RefreshIndicator(
                  onRefresh: _fetchTrackingData,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Order Header Card
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: AppColors.border),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    _orderData!.orderCode,
                                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppColors.primary),
                                  ),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                    decoration: BoxDecoration(
                                      color: AppColors.primaryLight,
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: Text(
                                      _orderData!.statusLabel,
                                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.primaryDark),
                                    ),
                                  ),
                                ],
                              ),
                              const Divider(height: 20),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  const Text('Total Tagihan', style: TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                                  Text(
                                    Formatters.formatRupiah(_orderData!.totalPrice),
                                    style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 4),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  const Text('Status Pembayaran', style: TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                                  Text(
                                    _orderData!.paymentStatus == 'paid' ? 'LUNAS' : 'BELUM DIBAYAR',
                                    style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: _orderData!.paymentStatus == 'paid' ? AppColors.success : AppColors.warning,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 16),

                        // Courier Card if assigned
                        if (_orderData!.courier != null) ...[
                          Container(
                            padding: const EdgeInsets.all(14),
                            decoration: BoxDecoration(
                              color: const Color(0xFFF3E8FF), // Light purple
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: const Color(0xFFD8B4FE)),
                            ),
                            child: Row(
                              children: [
                                const CircleAvatar(
                                  backgroundColor: Color(0xFF9333EA),
                                  child: Icon(Icons.two_wheeler, color: Colors.white, size: 20),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Text('Kurir Penjemput / Pengantar', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF7E22CE))),
                                      Text(_orderData!.courier!.name, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF581C87))),
                                      Text(_orderData!.courier!.phone ?? '', style: const TextStyle(fontSize: 11, color: Color(0xFF7E22CE))),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 16),
                        ],

                        // Timeline Stepper Card
                        Container(
                          padding: const EdgeInsets.all(18),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: AppColors.border),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  const Text('Timeline Pengerjaan', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                                  Text(
                                    'Tahap ${_orderData!.statusStep} dari 9',
                                    style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.primary),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 16),
                              TimelineStepper(steps: _orderData!.timeline),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
    );
  }
}

