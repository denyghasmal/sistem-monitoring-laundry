import 'package:flutter/foundation.dart';
import '../../../core/constants/api_endpoints.dart';
import '../../../core/network/api_client.dart';

class FcmService {
  static final FcmService _instance = FcmService._internal();
  factory FcmService() => _instance;

  FcmService._internal();

  /// Initialize Firebase messaging listeners & sync token with backend
  Future<void> initialize() async {
    debugPrint('[FCM] Initializing push notification service...');
  }

  /// Sends device token to Laravel backend
  Future<void> syncTokenWithBackend(String token) async {
    try {
      final api = ApiClient();
      await api.post(ApiEndpoints.fcmToken, data: {
        'fcm_token': token,
        'device_type': defaultTargetPlatform.name,
      });
      debugPrint('[FCM] Token successfully synced with Laravel backend.');
    } catch (e) {
      debugPrint('[FCM] Error syncing FCM token: $e');
    }
  }
}

