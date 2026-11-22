import 'package:intl/intl.dart';

class Formatters {
  static String formatRupiah(num? amount) {
    if (amount == null) return 'Rp 0';
    final formatter = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp ',
      decimalDigits: 0,
    );
    return formatter.format(amount);
  }

  static String formatDateTime(String? dateString) {
    if (dateString == null) return '-';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('d MMM yyyy, HH:mm', 'id_ID').format(date);
    } catch (_) {
      return dateString;
    }
  }
}

