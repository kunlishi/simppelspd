package com.example.attendance.web;

import com.example.attendance.model.User;
import com.example.attendance.repo.UserRepository;
import com.example.attendance.web.dto.ChangePasswordRequest;
import com.example.attendance.web.dto.UpdateProfileRequest;
import com.example.attendance.web.dto.UserProfileResponse;
import jakarta.validation.Valid;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/user")
public class UserController {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;

    public UserController(UserRepository userRepository, PasswordEncoder passwordEncoder) {
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
    }

    @GetMapping("/me")
    public ResponseEntity<UserProfileResponse> me(@AuthenticationPrincipal UserDetails principal) {
        User user = userRepository.findByUsername(principal.getUsername()).orElseThrow();
        return ResponseEntity.ok(UserProfileResponse.from(user));
    }

    @PutMapping("/profile")
    @Transactional
    public ResponseEntity<UserProfileResponse> updateProfile(@AuthenticationPrincipal UserDetails principal,
                                                             @Valid @RequestBody UpdateProfileRequest req) {
        User user = userRepository.findByUsername(principal.getUsername()).orElseThrow();
        if (req.getFullName() != null) user.setFullName(req.getFullName());
        if (req.getKelas() != null) user.setKelas(req.getKelas());
        if (req.getEmail() != null) user.setEmail(req.getEmail());
        return ResponseEntity.ok(UserProfileResponse.from(user));
    }

    @PostMapping("/change-password")
    @Transactional
    public ResponseEntity<Void> changePassword(@AuthenticationPrincipal UserDetails principal,
                                               @Valid @RequestBody ChangePasswordRequest req) {
        User user = userRepository.findByUsername(principal.getUsername()).orElseThrow();
        if (!passwordEncoder.matches(req.getOldPassword(), user.getPassword())) {
            return ResponseEntity.badRequest().build();
        }
        user.setPassword(passwordEncoder.encode(req.getNewPassword()));
        return ResponseEntity.ok().build();
    }

    @DeleteMapping("/delete")
    @Transactional
    public ResponseEntity<Void> deleteAccount(@AuthenticationPrincipal UserDetails principal) {
        User user = userRepository.findByUsername(principal.getUsername()).orElseThrow();
        userRepository.delete(user);
        return ResponseEntity.noContent().build();
    }
}
