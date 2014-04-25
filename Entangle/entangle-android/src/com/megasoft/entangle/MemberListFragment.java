package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;

/*
 * A fragment for the member list.
 * @author Omar ElAzazy
 */
public class MemberListFragment extends Fragment {

	private int tangleId;
	private String sessionId;
	private String[] memberNames;
	private int[] memberBalances;
	private String[] memberAvatarURLs;
	private int[] memberIds;
	private LinearLayout memberListView;
	private int numberOfMembers;
	private ViewGroup container;
	
	public ViewGroup getContainer() {
		return container;
	}

	public void setContainer(ViewGroup container) {
		this.container = container;
	}

	public int getNumberOfMembers() {
		return numberOfMembers;
	}

	public void setNumberOfMembers(int numberOfMembers) {
		this.numberOfMembers = numberOfMembers;
	}

	public LinearLayout getMemberListView() {
		return memberListView;
	}

	public void setMemberListView(LinearLayout memberListView) {
		this.memberListView = memberListView;
	}

	public String[] getMemberNames() {
		return memberNames;
	}

	public void setMemberNames(String[] memberNames) {
		this.memberNames = memberNames;
	}

	
}