package com.megasoft.entangle;

import java.util.HashMap;

import android.annotation.SuppressLint;
import android.app.DialogFragment;
import android.os.Bundle;
import android.view.ContextMenu;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ContextMenu.ContextMenuInfo;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;

@SuppressLint("NewApi")
public class FilteringFragment extends DialogFragment {

	private String rootResource = "http://entangle2.apiary.io/";

	private HashMap<String, Integer> userToId = new HashMap<String, Integer>();

	private HashMap<String, Integer> tagToId = new HashMap<String, Integer>();

	public static FilteringFragment createInstance(
			HashMap<String, Integer> tagToId, HashMap<String, Integer> userToId) {
		FilteringFragment fragment = new FilteringFragment();
		fragment.tagToId = tagToId;
		fragment.userToId = userToId;
		return fragment;
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		View view = inflater.inflate(R.layout.filtering_fragment, container,
				false);
		setTagSuggestions(view);
		setUserSuggestions(view);
		return view;
	}

	private void setTagSuggestions(View view) {
		AutoCompleteTextView tag = (AutoCompleteTextView) view
				.findViewById(R.id.tagValue);
		tag.setAdapter(new ArrayAdapter<String>(getActivity(),
				android.R.layout.simple_list_item_1,
				((TangleProfilePage) getActivity()).getTagsSuggestions()));
	}

	private void setUserSuggestions(View view) {
		AutoCompleteTextView user = (AutoCompleteTextView) view
				.findViewById(R.id.userValue);
		user.setAdapter(new ArrayAdapter<String>(getActivity(),
				android.R.layout.simple_list_item_1,
				((TangleProfilePage) getActivity()).getUsersSuggestions()));
	}

	

}
