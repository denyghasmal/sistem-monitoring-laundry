import 'service_model.dart';

class OrderModel {
  final int id;
  final String orderCode;
  final ServiceModel? service;
  final String status;
  final String statusLabel;
  final int statusStep;
  final double totalPrice;
  final double? totalWeight;
  final String paymentStatus;
  final String paymentMethod;
  final String? customerNotes;
  final String? createdAt;
  final List<TimelineStepModel> timeline;
  final CourierInfoModel? courier;

  OrderModel({
    required this.id,
    required this.orderCode,
    this.service,
    required this.status,
    required this.statusLabel,
    required this.statusStep,
    required this.totalPrice,
    this.totalWeight,
    required this.paymentStatus,
    required this.paymentMethod,
    this.customerNotes,
    this.createdAt,
    this.timeline = const [],
    this.courier,
  });

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    return OrderModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      orderCode: json['order_code'] ?? '',
      service: json['service'] != null ? ServiceModel.fromJson(json['service']) : null,
      status: json['status'] ?? 'pending',
      statusLabel: json['status_label'] ?? 'Menunggu',
      statusStep: json['status_step'] is int ? json['status_step'] : 1,
      totalPrice: (json['total_price'] as num?)?.toDouble() ?? 0.0,
      totalWeight: (json['total_weight'] as num?)?.toDouble(),
      paymentStatus: json['payment_status'] ?? 'unpaid',
      paymentMethod: json['payment_method'] ?? 'cash',
      customerNotes: json['customer_notes'],
      createdAt: json['created_at'],
      timeline: (json['timeline'] as List<dynamic>?)
              ?.map((e) => TimelineStepModel.fromJson(e))
              .toList() ??
          [],
      courier: json['courier'] != null ? CourierInfoModel.fromJson(json['courier']) : null,
    );
  }
}

class TimelineStepModel {
  final int step;
  final String statusKey;
  final String title;
  final String description;
  final String? timestamp;
  final bool isCompleted;
  final bool isCurrent;

  TimelineStepModel({
    required this.step,
    required this.statusKey,
    required this.title,
    required this.description,
    this.timestamp,
    required this.isCompleted,
    required this.isCurrent,
  });

  factory TimelineStepModel.fromJson(Map<String, dynamic> json) {
    return TimelineStepModel(
      step: json['step'] is int ? json['step'] : int.parse(json['step'].toString()),
      statusKey: json['status_key'] ?? '',
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      timestamp: json['timestamp'],
      isCompleted: json['is_completed'] == true,
      isCurrent: json['is_current'] == true,
    );
  }
}

class CourierInfoModel {
  final String name;
  final String? phone;
  final String? avatar;

  CourierInfoModel({
    required this.name,
    this.phone,
    this.avatar,
  });

  factory CourierInfoModel.fromJson(Map<String, dynamic> json) {
    return CourierInfoModel(
      name: json['name'] ?? 'Kurir',
      phone: json['phone'],
      avatar: json['avatar'],
    );
  }
}

