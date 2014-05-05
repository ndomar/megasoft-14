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

	public void setMemberAvatarView(ImageView memberAvatarView) {
		this.memberAvatarView = memberAvatarView;
	}
	
	public String getMemberAvatarURL() {
		return memberAvatarURL;
	}

	public void setMemberAvatarURL(String memberAvatarURL) {
		this.memberAvatarURL = memberAvatarURL;
	}

	public String getMemberName() {
		return memberName;
	}

	public void setMemberName(String memberName) {
		this.memberName = memberName;
	}

	public int getMemberBalance() {
		return memberBalance;
	}

	public void setMemberBalance(int memberBalance) {
		this.memberBalance = memberBalance;
	}

	public TextView getMemberNameView() {
		return memberNameView;
	}

	public void setMemberNameView(TextView memberNameView) {
		this.memberNameView = memberNameView;
	}

	public TextView getMemberBalanceView() {
		return memberBalanceView;
	}

	public void setMemberBalanceView(TextView memberBalanceView) {
		this.memberBalanceView = memberBalanceView;
	}

	public int getMemberId() {
		return memberId;
	}

	public void setMemberId(int memberId) {
		this.memberId = memberId;
	}

	public int getTangleId() {
		return tangleId;
	}

	public void setTangleId(int tangleId) {
		this.tangleId = tangleId;
	}

	@Override 
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		
		setMemberId(getArguments().getInt(Config.USER_ID, -1));
		setTangleId(getArguments().getInt(Config.TANGLE_ID, -1));
		setMemberName(getArguments().getString(Config.MEMBER_NAME));
		setMemberBalance(getArguments().getInt(Config.MEMBER_BALANCE, 0));
		setMemberAvatarURL(getArguments().getString(Config.MEMBER_AVATAR_URL));
		
		View view = inflater.inflate(R.layout.fragment_member_entry,
				container, false);
		
		view.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View view) {
				Intent profileIntent = new Intent(getActivity().getBaseContext(), ProfileActivity.class);
				profileIntent.putExtra("tangle id", getTangleId());
				profileIntent.putExtra("user id", getMemberId());
				
				startActivity(profileIntent);
			}
		});
		
		setMemberNameView((TextView) view.findViewById(R.id.memberName));
		setMemberBalanceView((TextView) view.findViewById(R.id.memberBalance));
		setMemberAvatarView((ImageView) view.findViewById(R.id.memberAvatar));

		getMemberNameView().setText(getMemberName());
		getMemberBalanceView().setText(getMemberBalance() + "");
		try{ 
			ImageRequest imageRequest = new ImageRequest(getMemberAvatarView());
			imageRequest.execute(getMemberAvatarURL());
		} catch(Exception e){
			toasterShow("Fetching photo is not working ...");
		}
		return view;
	}
	
	/*
	 * It shows the given message in a toaster
	 * @param String message, the message to be showed
	 * @author Omar ElAzazy
	 */
	private void toasterShow(String message){
		Toast.makeText(getActivity().getBaseContext(),
				message,
				Toast.LENGTH_LONG).show();
	}
}
