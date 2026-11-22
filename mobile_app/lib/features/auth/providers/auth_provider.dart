import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../../core/constants/api_endpoints.dart';
import '../../../core/network/api_client.dart';
import '../models/user_model.dart';

class AuthProvider extends ChangeNotifier {
  final ApiClient _api = ApiClient();

  UserModel? _user;
  String? _token;
  bool _isLoading = false;
  String? _errorMessage;

  UserModel? get user => _user;
  String? get token => _token;
  bool get isAuthenticated => _token != null && _token!.isNotEmpty;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> tryAutoLogin() async {
    final prefs = await SharedPreferences.getInstance();
    final savedToken = prefs.getString('auth_token');
    if (savedToken != null && savedToken.isNotEmpty) {
      _token = savedToken;
      try {
        final res = await _api.get(ApiEndpoints.me);
        if (res.data['success'] == true) {
          _user = UserModel.fromJson(res.data['data']);
        }
      } catch (_) {
        await logout();
      }
      notifyListeners();
    }
  }

  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _api.post(ApiEndpoints.login, data: {
        'email': email,
        'password': password,
      });

      if (res.data['success'] == true) {
        _token = res.data['data']['token'];
        _user = UserModel.fromJson(res.data['data']['user']);

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);

        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = res.data['message'] ?? 'Login gagal';
      }
    } catch (e) {
      _errorMessage = 'Terjadi kesalahan koneksi server: $e';
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<bool> register(String name, String email, String phone, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _api.post(ApiEndpoints.register, data: {
        'name': name,
        'email': email,
        'phone': phone,
        'password': password,
      });

      if (res.data['success'] == true) {
        _token = res.data['data']['token'];
        _user = UserModel.fromJson(res.data['data']['user']);

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);

        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = res.data['message'] ?? 'Registrasi gagal';
      }
    } catch (e) {
      _errorMessage = 'Koneksi gagal: $e';
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<void> logout() async {
    try {
      await _api.post(ApiEndpoints.logout);
    } catch (_) {}

    _user = null;
    _token = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    notifyListeners();
  }
}

