<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$report_id = $_GET['id'] ?? 0;

// ดึงข้อมูลรายงาน
$stmt = $pdo->prepare("
    SELECT dr.*, s.staff_name, s.position 
    FROM daily_reports dr 
    LEFT JOIN it_staff s ON dr.staff_id = s.staff_id 
    WHERE dr.report_id = ?
");
$stmt->execute([$report_id]);
$report = $stmt->fetch();

if (!$report) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการทำงาน - <?php echo htmlspecialchars($report['staff_name']); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .report-container { box-shadow: none !important; }
        }
        
        .report-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 800px;
        }
        
        .report-header {
            text-align: center;
            border-bottom: 3px solid #3498db;
            padding-bottom: 30px;
            margin-bottom: 40px;
        }
        
        .report-section {
            margin-bottom: 30px;
        }
        
        .report-section h4 {
            color: #2c3e50;
            border-left: 4px solid #3498db;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        
        .report-content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #e9ecef;
            line-height: 1.8;
        }
        
        .report-meta {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .report-meta .row div {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print" style="margin: 20px 0;">
            <a href="../index.php" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> กลับหน้าหลัก
            </a>
            <div class="pull-right">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fa fa-print"></i> พิมพ์รายงาน
                </button>
                <button onclick="savePDF()" class="btn btn-success">
                    <i class="fa fa-file-pdf-o"></i> บันทึก PDF
                </button>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="report-container">
            <div class="report-header">
                <h2><i class="fa fa-file-text"></i> รายงานการทำงานรายวัน</h2>
                <h3>แผนกเทคโนโลยีสารสนเทศ</h3>
            </div>
            
            <div class="report-meta">
                <div class="row">
                    <div class="col-md-6">
                        <strong><i class="fa fa-user"></i> ชื่อ-นามสกุล:</strong><br>
                        <?php echo htmlspecialchars($report['staff_name']); ?>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="fa fa-briefcase"></i> ตำแหน่ง:</strong><br>
                        <?php echo htmlspecialchars($report['position']); ?>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="fa fa-calendar"></i> วันที่รายงาน:</strong><br>
                        <?php echo date('d/m/Y', strtotime($report['report_date'])); ?>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="fa fa-clock-o"></i> ชั่วโมงทำงาน:</strong><br>
                        <?php echo $report['working_hours']; ?> ชั่วโมง
                        <?php if ($report['overtime_hours'] > 0): ?>
                            (ล่วงเวลา <?php echo $report['overtime_hours']; ?> ชั่วโมง)
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <?php if ($report['work_summary']): ?>
            <div class="report-section">
                <h4><i class="fa fa-summary"></i> สรุปการทำงาน</h4>
                <div class="report-content">
                    <?php echo nl2br(htmlspecialchars($report['work_summary'])); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($report['tasks_completed']): ?>
            <div class="report-section">
                <h4><i class="fa fa-check-circle"></i> งานที่เสร็จสิ้น</h4>
                <div class="report-content">
                    <?php echo nl2br(htmlspecialchars($report['tasks_completed'])); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($report['tasks_in_progress']): ?>
            <div class="report-section">
                <h4><i class="fa fa-clock-o"></i> งานที่กำลังดำเนินการ</h4>
                <div class="report-content">
                    <?php echo nl2br(htmlspecialchars($report['tasks_in_progress'])); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($report['problems_encountered']): ?>
            <div class="report-section">
                <h4><i class="fa fa-exclamation-triangle"></i> ปัญหาที่พบ</h4>
                <div class="report-content">
                    <?php echo nl2br(htmlspecialchars($report['problems_encountered'])); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($report['solutions_applied']): ?>
            <div class="report-section">
                <h4><i class="fa fa-lightbulb-o"></i> วิธีการแก้ไข</h4>
                <div class="report-content">
                    <?php echo nl2br(htmlspecialchars($report['solutions_applied'])); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($report['next_day_plan']): ?>
            <div class="report-section">
                <h4><i class="fa fa-calendar-plus-o"></i> แผนการทำงานวันถัดไป</h4>
                <div class="report-content">
                    <?php echo nl2br(htmlspecialchars($report['next_day_plan'])); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div style="margin-top: 50px; text-align: center; color: #7f8c8d;">
                <p>รายงานนี้สร้างโดยระบบรายงานการทำงานแผนกไอที</p>
                <p>วันที่พิมพ์: <?php echo date('d/m/Y H:i:s'); ?></p>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function savePDF() {
            const { jsPDF } = window.jspdf;
            
            html2canvas(document.querySelector('.report-container'), {
                scale: 2,
                useCORS: true
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgWidth = 210;
                const pageHeight = 295;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                let heightLeft = imgHeight;
                let position = 0;

                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                const fileName = 'รายงานการทำงาน_<?php echo date("d-m-Y", strtotime($report["report_date"])); ?>_<?php echo str_replace(" ", "_", $report["staff_name"]); ?>.pdf';
                pdf.save(fileName);
            });
        }
    </script>
</body>
</html>