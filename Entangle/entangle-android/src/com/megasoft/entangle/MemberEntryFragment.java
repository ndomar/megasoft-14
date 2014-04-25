package com.megasoft.entangle;

import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.ImageRequest;

/*
 * A fragment one entry of a member in the member list that populates itself
 * @author Omar ElAzazy
 */
public class MemberEntryFragment extends Fragment {
	
	private int memberId;
	private int tangleId;
	private String memberName;
	private int memberBalance;
	private TextView memberNameView;
	private TextView memberBalanceView;
	private ImageView memberAvatarView;
	private String memberAvatarURL;
	
	public ImageView getMemberAvatarView() {
		return memberAvatarView;
	}

	
}
