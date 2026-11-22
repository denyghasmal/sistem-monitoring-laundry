class ServiceModel {
  final int id;
  final String name;
  final String category;
  final double pricePerUnit;
  final String unit;
  final int estimatedHours;
  final String? description;
  final String? icon;

  ServiceModel({
    required this.id,
    required this.name,
    required this.category,
    required this.pricePerUnit,
    required this.unit,
    required this.estimatedHours,
    this.description,
    this.icon,
  });

  factory ServiceModel.fromJson(Map<String, dynamic> json) {
    return ServiceModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      name: json['name'] ?? '',
      category: json['category'] ?? 'kiloan',
      pricePerUnit: (json['price_per_unit'] as num).toDouble(),
      unit: json['unit'] ?? 'kg',
      estimatedHours: json['estimated_hours'] is int ? json['estimated_hours'] : int.parse(json['estimated_hours'].toString()),
      description: json['description'],
      icon: json['icon'],
    );
  }
}

