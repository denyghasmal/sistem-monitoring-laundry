import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';
import '../../order/models/order_model.dart';

class TimelineStepper extends StatelessWidget {
  final List<TimelineStepModel> steps;

  const TimelineStepper({
    super.key,
    required this.steps,
  });

  @override
  Widget build(BuildContext context) {
    if (steps.isEmpty) {
      return const Center(child: Text('Belum ada data status timeline.'));
    }

    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: steps.length,
      itemBuilder: (context, index) {
        final step = steps[index];
        final isLast = index == steps.length - 1;

        return IntrinsicHeight(
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Icon Indicator & Vertical Connector
              Column(
                children: [
                  Container(
                    width: 28,
                    height: 28,
                    decoration: BoxDecoration(
                      color: step.isCompleted
                          ? (step.isCurrent ? AppColors.primary : AppColors.success)
                          : Colors.grey.shade200,
                      shape: BoxShape.circle,
                      border: step.isCurrent
                          ? Border.all(color: AppColors.primaryLight, width: 4)
                          : null,
                    ),
                    child: Center(
                      child: step.isCompleted && !step.isCurrent
                          ? const Icon(Icons.check, size: 14, color: Colors.white)
                          : Text(
                              '${step.step}',
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: step.isCompleted ? Colors.white : Colors.grey.shade600,
                              ),
                            ),
                    ),
                  ),
                  if (!isLast)
                    Expanded(
                      child: Container(
                        width: 2,
                        margin: const EdgeInsets.symmetric(vertical: 4),
                        color: step.isCompleted && !step.isCurrent
                            ? AppColors.success
                            : Colors.grey.shade300,
                      ),
                    ),
                ],
              ),
              const SizedBox(width: 14),

              // Step Detail Text
              Expanded(
                child: Padding(
                  padding: EdgeInsets.only(bottom: isLast ? 0 : 20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            step.title,
                            style: TextStyle(
                              fontSize: 13,
                              fontWeight: step.isCurrent ? FontWeight.bold : FontWeight.w600,
                              color: step.isCurrent
                                  ? AppColors.primaryDark
                                  : (step.isCompleted ? AppColors.textPrimary : AppColors.textSecondary),
                            ),
                          ),
                          if (step.timestamp != null)
                            Text(
                              step.timestamp!,
                              style: const TextStyle(
                                fontSize: 10,
                                color: AppColors.textSecondary,
                              ),
                            ),
                        ],
                      ),
                      const SizedBox(height: 3),
                      Text(
                        step.description,
                        style: TextStyle(
                          fontSize: 11,
                          color: step.isCurrent ? AppColors.textPrimary : AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}

