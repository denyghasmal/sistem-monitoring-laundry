import 'package:flutter/material.dart';
import '../../../core/constants/api_endpoints.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/network/api_client.dart';
import '../../../core/utils/formatters.dart';
import '../models/service_model.dart';
import '../../tracking/screens/order_tracking_screen.dart';

class OrderCreateScreen extends StatefulWidget {
  final int? preselectedServiceId;

  const OrderCreateScreen({super.key, this.preselectedServiceId});

  @override
  State<OrderCreateScreen> createState() => _OrderCreateScreenState();
}

class _OrderCreateScreenState extends State<OrderCreateScreen> {
  final _api = ApiClient();
  final _notesController = TextEditingController();
  final _weightController = TextEditingController(text: '3.5');

  List<ServiceModel> _services = [];
  ServiceModel? _selectedService;
  String _deliveryType = 'pickup_delivery';
  String _paymentMethod = 'qris';
  bool _isLoading = true;
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _fetchServices();
  }

  Future<void> _fetchServices() async {
    try {
      final res = await _api.get(ApiEndpoints.services);
      if (res.data['success'] == true) {
        _services = (res.data['data'] as List)
            .map((e) => ServiceModel.fromJson(e))
            .toList();

        if (_services.isNotEmpty) {
          if (widget.preselectedServiceId != null) {
            _selectedService = _services.firstWhere(
              (s) => s.id == widget.preselectedServiceId,
              orElse: () => _services.first,
            );
          } else {
            _selectedService = _services.first;
          }
        }
      }
    } catch (_) {}

    if (mounted) setState(() => _isLoading = false);
  }

  double get _deliveryFee => (_deliveryType == 'pickup_delivery') ? 10000.0 : 0.0;
  double get _estimatedWeight => double.tryParse(_weightController.text) ?? 1.0;
  double get _estimatedTotal {
    if (_selectedService == null) return 0;
    return (_estimatedWeight * _selectedService!.pricePerUnit) + _deliveryFee;
  }

  void _submitOrder() async {
    if (_selectedService == null) return;
    setState(() => _isSubmitting = true);

    try {
      final res = await _api.post(ApiEndpoints.orders, data: {
        'service_id': _selectedService!.id,
        'delivery_type': _deliveryType,
        'estimated_weight': _estimatedWeight,
        'customer_notes': _notesController.text.trim(),
        'payment_method': _paymentMethod,
      });

      if (res.data['success'] == true && mounted) {
        final orderId = res.data['data']['id'];
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Pesanan laundry berhasil dibuat!')),
        );
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (_) => OrderTrackingScreen(orderId: orderId),
          ),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(res.data['message'] ?? 'Gagal membuat pesanan')),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Kesalahan jaringan: $e')),
      );
    }

    if (mounted) setState(() => _isSubmitting = false);
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    return Scaffold(
      appBar: AppBar(title: const Text('Buat Pesanan Laundry')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Service Selector
            const Text('Pilih Layanan', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.textPrimary)),
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.border),
              ),
              child: DropdownButtonHideUnderline(
                child: DropdownButton<ServiceModel>(
                  value: _selectedService,
                  isExpanded: true,
                  items: _services.map((s) {
                    return DropdownMenuItem(
                      value: s,
                      child: Text('${s.name} (${Formatters.formatRupiah(s.pricePerUnit)}/${s.unit})', style: const TextStyle(fontSize: 13)),
                    );
                  }).toList(),
                  onChanged: (val) => setState(() => _selectedService = val),
                ),
              ),
            ),
            const SizedBox(height: 18),

            // Delivery Method
            const Text('Metode Antar-Jemput', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.textPrimary)),
            const SizedBox(height: 8),
            Row(
              children: [
                Expanded(
                  child: GestureDetector(
                    onTap: () => setState(() => _deliveryType = 'pickup_delivery'),
                    child: Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: _deliveryType == 'pickup_delivery' ? AppColors.primaryLight : Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: _deliveryType == 'pickup_delivery' ? AppColors.primary : AppColors.border,
                          width: _deliveryType == 'pickup_delivery' ? 2 : 1,
                        ),
                      ),
                      child: Column(
                        children: const [
                          Icon(Icons.two_wheeler, color: AppColors.primary),
                          SizedBox(height: 6),
                          Text('Antar Jemput', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                          Text('Kurir (+10rb)', style: TextStyle(fontSize: 10, color: AppColors.textSecondary)),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: GestureDetector(
                    onTap: () => setState(() => _deliveryType = 'dropoff_pickup'),
                    child: Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: _deliveryType == 'dropoff_pickup' ? AppColors.primaryLight : Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: _deliveryType == 'dropoff_pickup' ? AppColors.primary : AppColors.border,
                          width: _deliveryType == 'dropoff_pickup' ? 2 : 1,
                        ),
                      ),
                      child: Column(
                        children: const [
                          Icon(Icons.storefront, color: AppColors.primary),
                          SizedBox(height: 6),
                          Text('Drop-off Outlet', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                          Text('Bawa Sendiri', style: TextStyle(fontSize: 10, color: AppColors.textSecondary)),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 18),

            // Estimated Weight
            const Text('Perkiraan Bobot / Satuan', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.textPrimary)),
            const SizedBox(height: 8),
            TextFormField(
              controller: _weightController,
              keyboardType: const TextInputType.numberWithOptions(decimal: true),
              onChanged: (_) => setState(() {}),
              decoration: InputDecoration(
                suffixText: _selectedService?.unit ?? 'kg',
                helperText: 'Bobot aktual akan ditimbang kasir saat pakaian tiba di gerai',
                helperStyle: const TextStyle(fontSize: 10),
              ),
            ),
            const SizedBox(height: 18),

            // Notes
            const Text('Catatan Pakaian (Opsional)', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.textPrimary)),
            const SizedBox(height: 8),
            TextFormField(
              controller: _notesController,
              maxLines: 2,
              decoration: const InputDecoration(
                hintText: 'Contoh: Pakaian putih dipisah, jangan gunakan pemutih...',
              ),
            ),
            const SizedBox(height: 18),

            // Payment Method Selector
            const Text('Metode Pembayaran', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.textPrimary)),
            const SizedBox(height: 8),
            Column(
              children: [
                RadioListTile<String>(
                  value: 'qris',
                  groupValue: _paymentMethod,
                  title: const Text('QRIS (BCA, GoPay, OVO, ShopeePay)', style: TextStyle(fontSize: 13)),
                  onChanged: (v) => setState(() => _paymentMethod = v!),
                  contentPadding: EdgeInsets.zero,
                ),
                RadioListTile<String>(
                  value: 'bank_transfer',
                  groupValue: _paymentMethod,
                  title: const Text('Virtual Account Bank', style: TextStyle(fontSize: 13)),
                  onChanged: (v) => setState(() => _paymentMethod = v!),
                  contentPadding: EdgeInsets.zero,
                ),
                RadioListTile<String>(
                  value: 'cash',
                  groupValue: _paymentMethod,
                  title: const Text('Tunai (COD / Di Gerai)', style: TextStyle(fontSize: 13)),
                  onChanged: (v) => setState(() => _paymentMethod = v!),
                  contentPadding: EdgeInsets.zero,
                ),
              ],
            ),
            const SizedBox(height: 16),

            // Cost Summary Card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.border),
              ),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Estimasi Tarif Layanan', style: TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                      Text(Formatters.formatRupiah(_estimatedWeight * (_selectedService?.pricePerUnit ?? 0)), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Ongkos Antar Jemput', style: TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                      Text(Formatters.formatRupiah(_deliveryFee), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
                    ],
                  ),
                  const Divider(height: 18),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Total Perkiraan Biaya', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                      Text(Formatters.formatRupiah(_estimatedTotal), style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppColors.primary)),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Submit Button
            ElevatedButton(
              onPressed: _isSubmitting ? null : _submitOrder,
              child: _isSubmitting
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('Konfirmasi & Buat Pesanan'),
            ),
          ],
        ),
      ),
    );
  }
}

