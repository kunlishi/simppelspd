package com.example.attendance.model;

import jakarta.persistence.*;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.Objects;

@Entity
@Table(name = "permits")
public class Permit {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(optional = false, fetch = FetchType.LAZY)
    @JoinColumn(name = "student_id")
    private User student;

    @Column(nullable = false)
    private LocalDate forDate;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 16)
    private PermitType type;

    @Column(length = 1024)
    private String reason;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 16)
    private PermitStatus status = PermitStatus.PENDING;

    @Column(length = 256)
    private String proofPath; // relative path under uploads

    @Column(length = 512)
    private String adminNote;

    @Column(nullable = false)
    private LocalDateTime createdAt = LocalDateTime.now();

    @Column(nullable = false)
    private LocalDateTime updatedAt = LocalDateTime.now();

    @PreUpdate
    public void onUpdate() { this.updatedAt = LocalDateTime.now(); }

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public User getStudent() { return student; }
    public void setStudent(User student) { this.student = student; }
    public LocalDate getForDate() { return forDate; }
    public void setForDate(LocalDate forDate) { this.forDate = forDate; }
    public PermitType getType() { return type; }
    public void setType(PermitType type) { this.type = type; }
    public String getReason() { return reason; }
    public void setReason(String reason) { this.reason = reason; }
    public PermitStatus getStatus() { return status; }
    public void setStatus(PermitStatus status) { this.status = status; }
    public String getProofPath() { return proofPath; }
    public void setProofPath(String proofPath) { this.proofPath = proofPath; }
    public String getAdminNote() { return adminNote; }
    public void setAdminNote(String adminNote) { this.adminNote = adminNote; }
    public LocalDateTime getCreatedAt() { return createdAt; }
    public void setCreatedAt(LocalDateTime createdAt) { this.createdAt = createdAt; }
    public LocalDateTime getUpdatedAt() { return updatedAt; }
    public void setUpdatedAt(LocalDateTime updatedAt) { this.updatedAt = updatedAt; }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (!(o instanceof Permit permit)) return false;
        return Objects.equals(id, permit.id);
    }

    @Override
    public int hashCode() {
        return Objects.hashCode(id);
    }
}
