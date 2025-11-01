package com.example.presensi.service;

import com.example.presensi.dto.UserDtos;
import com.example.presensi.model.User;
import com.example.presensi.repository.UserRepository;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
public class UserService {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;

    public UserService(UserRepository userRepository, PasswordEncoder passwordEncoder) {
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
    }

    public User getByEmail(String email) {
        return userRepository.findByEmail(email).orElseThrow(() -> new IllegalArgumentException("Pengguna tidak ditemukan"));
    }

    @Transactional
    public User updateProfile(User user, UserDtos.UpdateProfileRequest req) {
        user.setName(req.getName());
        if (!user.getEmail().equals(req.getEmail())) {
            if (userRepository.existsByEmail(req.getEmail())) {
                throw new IllegalArgumentException("Email sudah digunakan");
            }
            user.setEmail(req.getEmail());
        }
        return userRepository.save(user);
    }

    @Transactional
    public void changePassword(User user, UserDtos.ChangePasswordRequest req) {
        if (!passwordEncoder.matches(req.getOldPassword(), user.getPasswordHash())) {
            throw new IllegalArgumentException("Password lama salah");
        }
        user.setPasswordHash(passwordEncoder.encode(req.getNewPassword()));
        userRepository.save(user);
    }

    @Transactional
    public void deleteAccount(User user) {
        userRepository.delete(user);
    }
}
