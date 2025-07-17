$(document).ready(function() {
    // Initialize current date
    $('#report-date').val(new Date().toISOString().split('T')[0]);
    
    // Tab switching with smooth animation
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $($(e.target).attr('href')).find('.panel').each(function(index) {
            $(this).delay(index * 100).queue(function() {
                $(this).addClass('animated fadeInUp').dequeue();
            });
        });
    });
    
    // Search functionality for reports
    $('#search').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#reports-table tbody tr, .table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Staff filter
    $('#staff-filter').on('change', function() {
        var staffId = $(this).val();
        if (staffId === '') {
            $('.table tbody tr').show();
        } else {
            $('.table tbody tr').hide();
            $('.table tbody tr').each(function() {
                var rowStaff = $(this).find('td:nth-child(2)').text();
                var selectedStaffName = $('#staff-filter option:selected').text();
                if (rowStaff.includes(selectedStaffName)) {
                    $(this).show();
                }
            });
        }
    });
    
    // Date filter
    $('#date-filter').on('change', function() {
        var selectedDate = $(this).val();
        if (selectedDate === '') {
            $('.table tbody tr').show();
        } else {
            var formattedDate = formatDateThai(selectedDate);
            $('.table tbody tr').hide();
            $('.table tbody tr').each(function() {
                var rowDate = $(this).find('td:first').text();
                if (rowDate === formattedDate) {
                    $(this).show();
                }
            });
        }
    });
    
    // Form validation and submission
    $('#create-report-form').on('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        var staffId = $('#staff-select').val();
        var date = $('#report-date').val();
        var summary = $('#work-summary').val();
        var hours = $('#working-hours').val();
        
        if (!staffId || !date || !summary || !hours) {
            showNotification('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน', 'error');
            return;
        }
        
        // Simulate form submission
        showNotification('บันทึกรายงานรายวันเรียบร้อยแล้ว', 'success');
        
        // Add new row to reports table
        var staffName = $('#staff-select option:selected').text();
        addNewReportRow(staffName, date, summary, hours);
        
        // Reset form
        this.reset();
        $('#report-date').val(new Date().toISOString().split('T')[0]);
        
        // Switch to reports tab
        setTimeout(function() {
            $('a[href="#reports"]').tab('show');
        }, 1500);
    });
    
    // Save as draft
    $('.btn-warning').on('click', function() {
        if ($(this).closest('form').length) {
            showNotification('บันทึกร่างเรียบร้อยแล้ว', 'warning');
        }
    });
    
    // Preview report
    $('.btn-info').on('click', function() {
        if ($(this).closest('form').length) {
            showPreviewModal();
        }
    });
    
    // Delete confirmation
    $(document).on('click', '.btn-danger', function() {
        if ($(this).find('.fa-trash').length) {
            if (confirm('คุณต้องการลบรายการนี้หรือไม่?')) {
                $(this).closest('tr').fadeOut(300, function() {
                    $(this).remove();
                });
                showNotification('ลบรายการเรียบร้อยแล้ว', 'info');
            }
        }
    });
    
    // Print functionality
    $(document).on('click', '.btn-success', function() {
        if ($(this).find('.fa-print').length) {
            window.print();
        }
    });
    
    // Update task progress
    $(document).on('click', '.btn-success', function() {
        if ($(this).find('.fa-refresh').length) {
            var $progressBar = $(this).closest('tr').find('.progress-bar');
            var currentProgress = parseInt($progressBar.text());
            var newProgress = Math.min(currentProgress + 10, 100);
            
            $progressBar.css('width', newProgress + '%').text(newProgress + '%');
            
            if (newProgress === 100) {
                $(this).closest('tr').find('.label-primary').removeClass('label-primary').addClass('label-success').text('เสร็จสิ้น');
                showNotification('งานเสร็จสิ้นแล้ว!', 'success');
            } else {
                showNotification('อัพเดทความคืบหน้าแล้ว', 'info');
            }
        }
    });
    
    // Smooth scrolling for internal links
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 1000);
        }
    });
    
    // Auto-save draft every 30 seconds
    setInterval(function() {
        var summary = $('#work-summary').val();
        if (summary && summary.length > 10) {
            // Simulate auto-save
            console.log('Auto-saving draft...');
            showNotification('บันทึกร่างอัตโนมัติ', 'info', 2000);
        }
    }, 30000);
    
    // Character counter for textareas
    $('textarea').on('input', function() {
        var maxLength = 1000;
        var currentLength = $(this).val().length;
        var remaining = maxLength - currentLength;
        
        if (!$(this).next('.char-counter').length) {
            $(this).after('<small class="char-counter text-muted"></small>');
        }
        
        $(this).next('.char-counter').text('เหลือ ' + remaining + ' ตัวอักษร');
        
        if (remaining < 50) {
            $(this).next('.char-counter').removeClass('text-muted').addClass('text-warning');
        } else {
            $(this).next('.char-counter').removeClass('text-warning').addClass('text-muted');
        }
    });
    
    // Real-time clock
    function updateClock() {
        var now = new Date();
        var timeString = now.toLocaleTimeString('th-TH');
        var dateString = now.toLocaleDateString('th-TH');
        
        if (!$('.current-time').length) {
            $('.navbar-brand').after('<span class="navbar-text current-time" style="color: #ecf0f1; margin-left: 20px;"><i class="fa fa-clock-o"></i> ' + timeString + ' | ' + dateString + '</span>');
        } else {
            $('.current-time').html('<i class="fa fa-clock-o"></i> ' + timeString + ' | ' + dateString);
        }
    }
    
    updateClock();
    setInterval(updateClock, 1000);
    
    // Initialize tooltips
    $('[title]').tooltip();
    
    // Loading animation for buttons
    $('.btn').on('click', function() {
        var $btn = $(this);
        if (!$btn.hasClass('no-loading')) {
            var originalText = $btn.html();
            $btn.html('<span class="loading"></span> กำลังประมวลผล...').prop('disabled', true);
            
            setTimeout(function() {
                $btn.html(originalText).prop('disabled', false);
            }, 1000);
        }
    });
});

// Helper functions
function formatDateThai(dateString) {
    var date = new Date(dateString);
    var day = String(date.getDate()).padStart(2, '0');
    var month = String(date.getMonth() + 1).padStart(2, '0');
    var year = date.getFullYear();
    return day + '/' + month + '/' + year;
}

function showNotification(message, type, duration = 3000) {
    var notification = $('<div class="notification ' + type + '">' + message + '</div>');
    $('body').append(notification);
    
    setTimeout(function() {
        notification.addClass('show');
    }, 100);
    
    setTimeout(function() {
        notification.removeClass('show');
        setTimeout(function() {
            notification.remove();
        }, 300);
    }, duration);
}

function addNewReportRow(staffName, date, summary, hours) {
    var formattedDate = formatDateThai(date);
    var rowCount = $('.table tbody tr').length + 1;
    
    var newRow = '<tr>' +
                '<td>' + formattedDate + '</td>' +
                '<td>' + staffName + '</td>' +
                '<td>' + summary.substring(0, 50) + '...</td>' +
                '<td>' + hours + ' ชั่วโมง</td>' +
                '<td>' +
                    '<button class="btn btn-xs btn-info no-loading" title="ดู"><i class="fa fa-eye"></i></button> ' +
                    '<button class="btn btn-xs btn-warning no-loading" title="แก้ไข"><i class="fa fa-edit"></i></button> ' +
                    '<button class="btn btn-xs btn-success no-loading" title="พิมพ์"><i class="fa fa-print"></i></button> ' +
                '</td>' +
                '</tr>';
    
    $('#reports .table tbody').prepend(newRow);
    $('#reports .table tbody tr:first').hide().fadeIn(500);
}

function showPreviewModal() {
    var staffName = $('#staff-select option:selected').text();
    var date = $('#report-date').val();
    var summary = $('#work-summary').val();
    var completed = $('#tasks-completed').val();
    var progress = $('#tasks-progress').val();
    var problems = $('#problems').val();
    var solutions = $('#solutions').val();
    var nextPlan = $('#next-plan').val();
    var hours = $('#working-hours').val();
    var overtime = $('#overtime-hours').val();
    
    var previewContent = '<div class="modal fade" id="previewModal" tabindex="-1">' +
                        '<div class="modal-dialog modal-lg">' +
                        '<div class="modal-content">' +
                        '<div class="modal-header" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white;">' +
                        '<button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>' +
                        '<h4 class="modal-title"><i class="fa fa-file-text"></i> ตัวอย่างรายงานรายวัน</h4>' +
                        '</div>' +
                        '<div class="modal-body" style="padding: 30px;">' +
                        '<div class="text-center" style="margin-bottom: 30px;">' +
                        '<h3 style="color: #2c3e50;">รายงานการทำงานรายวัน</h3>' +
                        '<p><strong>เจ้าหน้าที่:</strong> ' + staffName + '</p>' +
                        '<p><strong>วันที่:</strong> ' + formatDateThai(date) + '</p>' +
                        '</div>' +
                        '<hr style="border-color: #3498db;">' +
                        '<h4 style="color: #e74c3c;"><i class="fa fa-summary"></i> สรุปการทำงาน</h4>' +
                        '<p style="background: #f8f9fa; padding: 15px; border-radius: 8px;">' + (summary || 'ไม่ได้ระบุ') + '</p>' +
                        (completed ? '<h4 style="color: #27ae60;"><i class="fa fa-check"></i> งานที่เสร็จสิ้น</h4><p style="background: #f8f9fa; padding: 15px; border-radius: 8px;">' + completed + '</p>' : '') +
                        (progress ? '<h4 style="color: #f39c12;"><i class="fa fa-clock-o"></i> งานที่กำลังดำเนินการ</h4><p style="background: #f8f9fa; padding: 15px; border-radius: 8px;">' + progress + '</p>' : '') +
                        (problems ? '<h4 style="color: #e74c3c;"><i class="fa fa-exclamation-triangle"></i> ปัญหาที่พบ</h4><p style="background: #f8f9fa; padding: 15px; border-radius: 8px;">' + problems + '</p>' : '') +
                        (solutions ? '<h4 style="color: #3498db;"><i class="fa fa-lightbulb-o"></i> วิธีการแก้ไข</h4><p style="background: #f8f9fa; padding: 15px; border-radius: 8px;">' + solutions + '</p>' : '') +
                        (nextPlan ? '<h4 style="color: #9b59b6;"><i class="fa fa-calendar"></i> แผนการทำงานวันถัดไป</h4><p style="background: #f8f9fa; padding: 15px; border-radius: 8px;">' + nextPlan + '</p>' : '') +
                        '<hr style="border-color: #3498db;">' +
                        '<div class="row">' +
                        '<div class="col-md-6"><strong>ชั่วโมงทำงาน:</strong> ' + (hours || '0') + ' ชั่วโมง</div>' +
                        '<div class="col-md-6"><strong>ชั่วโมงล่วงเวลา:</strong> ' + (overtime || '0') + ' ชั่วโมง</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                        '<button type="button" class="btn btn-primary no-loading" data-dismiss="modal"><i class="fa fa-check"></i> ตกลง</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
    
    $('body').append(previewContent);
    $('#previewModal').modal('show');
    
    $('#previewModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

// Initialize dashboard charts (if Chart.js is available)
function initDashboardCharts() {
    // This would initialize charts if Chart.js was included
    console.log('Dashboard charts would be initialized here');
}

// Export functions for potential use
window.ITWorkSystem = {
    formatDateThai: formatDateThai,
    showNotification: showNotification,
    addNewReportRow: addNewReportRow,
    showPreviewModal: showPreviewModal
};