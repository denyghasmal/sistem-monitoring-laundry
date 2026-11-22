class ApiEndpoints {
  // Use 10.0.2.2 for Android emulator, 127.0.0.1 for iOS simulator / web
  static const String baseUrl = 'http://10.0.2.2:8000/api/v1';

  // Auth
  static const String login = '/auth/login';
  static const String register = '/auth/register';
  static const String logout = '/auth/logout';
  static const String me = '/auth/me';
  static const String fcmToken = '/fcm-token';

  // Services
  static const String services = '/services';

  // Orders & Real-time Tracking
  static const String orders = '/orders';
  static String orderDetail(int id) => '/orders/$id';
  static String orderTrack(dynamic id) => '/orders/$id/track';

  // Addresses
  static const String addresses = '/addresses';

  // Payments
  static const String paymentsPay = '/payments/pay';
}

