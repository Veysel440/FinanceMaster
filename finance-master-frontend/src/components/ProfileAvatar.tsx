import Image from "next/image";

type ProfileAvatarProps = {
    user?: { profile_photo?: string | null };
    size?: number;
};

const ProfileAvatar = ({ user, size = 64 }: ProfileAvatarProps) => (
    <div>
        <Image
            src={user?.profile_photo || "/avatar.png"}
            width={size}
            height={size}
            alt="Profil"
            className="rounded-full border"
        />
    </div>
);
export default ProfileAvatar;
